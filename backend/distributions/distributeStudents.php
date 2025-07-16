<?php
require_once "../config/dbConfig.php";

require_once "Distribution.php";
require_once "../users/User.php";

if ($_SERVER['REQUEST_METHOD'] != 'POST') //check request method
{
    echo "only POST";
    exit;
}

if (!isset($_POST["action"]) || $_POST["action"] != "distributeStudents") //check action var
{
    echo "unauthorized access";
    exit;
}

//Try getting dist
try {
    $distribution = new Distribution($_POST["distID"], $mysqli);
} catch (Exception $e) {
    echo "Error getting distribution";
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

$dist_id = $distribution->getId();
$distChoices = getFromDBCondition("distribution_choices", "WHERE distribution = $dist_id and deleted = 0", $mysqli);


//if some student hasnt chosen - add scores of 1 for them
foreach ($users as $currUser) {
    $user_id = $currUser["id"];
    $studentScores = getFromDBCondition("s_d_scores", "WHERE user_id = $user_id AND distribution_id = $dist_id AND deleted = 0", $mysqli);
    if (count($studentScores) != count($distChoices)) { //if student hasnt chosen for something (most likely all)
        foreach ($distChoices as $currChoice) {
            $choice_id = $currChoice["id"];
            $currStartYear = $currUser["start_year"];
            $score = getFromDBCondition("s_d_scores", "WHERE user_id = $user_id AND distribution_id = $dist_id AND choice_id = $choice_id AND deleted = 0", $mysqli);
            if (!$score) { //if student doesnt have a score for this choice
                $mysqli->query("INSERT INTO s_d_scores (user_id, distribution_id, choice_id, score, user_start_year) VALUES ($user_id, $dist_id, $choice_id, 1, $currStartYear)");
            }
        }
    }
}

// 1) Build the “disciplines” array
$disciplines = [];
foreach ($distChoices as $disc) {
    $disciplines[] = [
        "id" => (int) $disc["id"],
        "min" => (int) $disc["min"],
        "max" => (int) $disc["max"]
    ];
}

// 2) Build the “students” array
$students = [];
foreach ($users as $stu) {
    $stu_id = (int) $stu["id"];
    $stu_fn = $stu["fn"];

    //grades
    $grade = getFromDBCondition("student_grades", "WHERE student_fn = '$stu_fn' AND semester = $semester_applicable AND deleted = 0", $mysqli);
    if (!$grade) { //stop if failed to take grade
        echo "failed to take grade for student with id: " . $stu_id;
        exit;
    }

    $grade = (float) $grade[0]["grade"];

    //scores
    $desires = [];
    $studentScores = getFromDBCondition("s_d_scores", "WHERE user_id = $stu_id AND distribution_id = $dist_id AND deleted = 0", $mysqli);
    foreach ($studentScores as $score) {
        $desires[$score["choice_id"]] = $score["score"];
    }

    $students[] = [
        "id" => $stu_id,
        "grade" => $grade,
        "desires" => $desires
    ];
}

// 3) Combine into one PHP array and echo as JSON
$inputData = [
    "disciplines" => $disciplines,
    "students" => $students
];

//write it to a file for the solver
file_put_contents(
    __DIR__ . "/../../pythonSolver/tmp/input.json",
    json_encode($inputData, JSON_PRETTY_PRINT)
);

//Run the solver
$venv_python = __DIR__ . '/../../pythonSolver/venv/Scripts/python.exe';
$solver = __DIR__ . '/../../pythonSolver/solver.py';
$input = __DIR__ . '/../../pythonSolver/tmp/input.json';
$output = __DIR__ . '/../../pythonSolver/tmp/output.json';

$cmd = escapeshellcmd("$venv_python $solver $input $output") . " 2>&1";
$result = shell_exec($cmd);

if (!file_exists($output)) {
    echo "Error: Output file not created.";
    exit;
}

//Write the output for distributed students in db
// Read the output file
$outputData = json_decode(file_get_contents($output), true);

if (!isset($outputData['assignments']) || !is_array($outputData['assignments'])) {
    echo 'Invalid output file';
    exit;
}


//Insert to db
$mysqli->begin_transaction();
$stmt = $mysqli->prepare("INSERT INTO distributed_students (student_id, dist_id, dist_choice_id) VALUES (?, ?, ?)");
if (!$stmt) {
    echo 'Prepare failed: ' . $mysqli->error;
    exit;
}
try {
    foreach ($outputData['assignments'] as $student_id => $dist_choice_id) {
        // 1. Check for non-deleted entry
        $existing = getFromDBCondition(
            "distributed_students",
            "WHERE student_id = $student_id AND dist_id = $dist_id AND deleted = 0",
            $mysqli
        );

        // 2. If rows exist delete them
        if (is_array($existing)) {
            foreach ($existing as $currExisting) {
                deleteFromDB("distributed_students", $currExisting['id'], $mysqli, 'id');
            }
        }

        // 3. Insert the new row
        $stmt->bind_param('iii', $student_id, $dist_id, $dist_choice_id);
        if (!$stmt->execute()) {
            throw new Exception("Insert failed for student $student_id: " . $stmt->error);
        }
    }
    $stmt->close();
    $mysqli->commit();
} catch (Exception $e) {
    $mysqli->rollback();
    echo "Transaction failed: " . $e->getMessage();
    exit;
}

//Delete tmp files
/* @unlink($input);
@unlink($output); */


//Success
echo 1;
exit;

