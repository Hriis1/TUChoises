<?php
require_once "../config/dbConfig.php";
require_once "../config/sessionConfig.php";

require_once "Distribution.php";
require_once "../users/User.php";

if ($_SERVER['REQUEST_METHOD'] != 'POST') //check request method
    exit;

if (!isset($_POST["action"]) || $_POST["action"] != "makeChoice") //check action var
    exit;


//Try getting user and dist
$user = null;
$dist = null;
try {
    $user = new User($_POST["userID"], $mysqli);
    $dist = new Distribution($_POST["distID"], $mysqli);
} catch (Exception $e) {
    exit;
}

//Check if user can make a choice
if ($dist->getStudentPermisions($user, $mysqli) != 1)
    exit;

//Check if dist choices match
if (!isset($_POST["rating"]))
    return false;

$submitedRatings = $_POST["rating"];
$distChoices = $dist->getChoices($mysqli);

if (sizeof($submitedRatings) != sizeof($distChoices)) //if the size of submitted choices doesnt match
    exit;

for ($i = 0; $i < sizeof($distChoices); $i++) { //check if ids match
    if (!isset($submitedRatings[$distChoices[$i]->getId()]))
        exit;
}

//Validation complete
$userID = $user->getId();
$start_year = $user->getStartYear();
$distID = $dist->getId();

$stmt = $mysqli->prepare("
    INSERT INTO s_d_scores (user_id, distribution_id, choice_id, score, user_start_year)
    VALUES (?, ?, ?, ?, ?)
");
$stmt->bind_param("iiiii", $userID, $distID, $id, $rating, $start_year);

$successCount = 0;
foreach ($submitedRatings as $id => $rating) {
    if ($stmt->execute()) {
        $successCount++;
    }
}
$stmt->close();

if ($successCount >= 1) {
    //Success
    //Send an alert
    $_SESSION['alert'] = [
        "type" => "success",
        "text" => "Choice made successfully!"
    ];
} else {
    //Error
    //Send an alert
    $_SESSION['alert'] = [
        "type" => "danger",
        "text" => "Error making a choice!"
    ];
}

echo '<meta http-equiv="refresh" content="0;url=../../distributions/myDistributions.php?condition=all">';