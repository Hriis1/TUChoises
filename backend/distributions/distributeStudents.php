<?php
require_once "../config/dbConfig.php";
require_once "../config/sessionConfig.php";

require_once "Distribution.php";
require_once "../users/User.php";

if ($_SERVER['REQUEST_METHOD'] != 'POST') //check request method
    exit;

if (!isset($_POST["action"]) || $_POST["action"] != "distributeStudents") //check action var
    exit;

if ($user->getRole() != 3) //user is not an admin
    exit;

//Try getting dist
try {
    $distribution = new Distribution($_POST["distID"], $mysqli);
} catch (Exception $e) {
    exit;
}

//Error checking done
$start_year_applicable = $distribution->getStartYearApplicable();
$semester_applicable = $distribution->getSemesterApplicable() - 1; //need the grade of the previous semester
$errorResponse = [];

//Check if every student that has to choose has chosen
//Select the students needed
$studentsCondition = "WHERE role = 1 AND start_year = $start_year_applicable AND deleted = 0";
if ($distribution->getType() == 1) {
    $major = $mysqli->real_escape_string($distribution->getMajorShort());
    $studentsCondition .= " AND major = '$major'";
} else {
    $faculty = $mysqli->real_escape_string($distribution->getFacultyShort());
    $studentsCondition .= " AND faculty = '$faculty'";
}

$users = getFromDBCondition(
    "users",
    $studentsCondition,
    $mysqli
);

$distChoices = getFromDBCondition("distribution_choices", "WHERE distribution = $id and deleted = 0", $mysqli);
$dist_id = $distribution->getId();

//if some student hasnt chosen - add scores of 1 for them
foreach ($users as $currUser) {
    $user_id = $currUser["id"];
    $studentScores = getFromDBCondition("s_d_scores", "WHERE user_id = $user_id AND distribution_id = $dist_id AND deleted = 0", $mysqli);
    if (count($studentScores) != count($distChoices)) { //if student hasnt chosen for something (most likely all)
        foreach ($distChoices as $currChoice) {
            $choice_id = $currChoice["id"];
            $score = getFromDBCondition("s_d_scores", "WHERE user_id = $user_id AND distribution_id = $dist_id AND choice_id = $choice_id AND deleted = 0", $mysqli);
            if (!$score) { //if student doesnt have a score for this choice
                $mysqli->query("INSERT INTO s_d_scores (user_id, distribution_id, choice_id, score) VALUES ($user_id, $dist_id, $choice_id, 1)");
            }
        }
    }
}