<?php

header('Content-Type: application/json');
require_once "../config/dbConfig.php";
require_once "../config/sessionConfig.php";
require_once "moodleAuthRequest.php";

function authenticate($userID)
{
    $_SESSION["userID"] = $userID;
    echo json_encode([1, 'user', 'Authentication successful']);
    exit;
}

//Get the data
$username = $_POST['username'];
$pass = $_POST['pass'];

//Get the user
$stmt = $mysqli->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) { //check if user exists
    echo json_encode([0, 'user', 'User does not exist']);
    exit;
}

if ($user['active'] != 1) { //if users acc is not activated
    $moodleAuthRes = moodleAuthRequest($username, $pass);
    if ($moodleAuthRes[0] == 0) { //Error authenticating with moodle
        echo json_encode([0, 'pass', 'Log in using your moodle credentials']);
        exit;
    } else { //Moodle log in success
        //Activate the account - set active = 1 and set the password
        $uId = $user["id"];
        $hashedPass = password_hash($pass, PASSWORD_BCRYPT);
        $stmt = $mysqli->prepare("UPDATE users SET active = 1, pass = ? WHERE id = ? AND username = ?");
        $stmt->bind_param("sis", $hashedPass, $uId, $username);
        $stmt->execute();
    }
} else {

    //Check the password
    if (!password_verify($pass, $user['pass'])) {
        echo json_encode([0, 'pass', 'Incorrect password']);
        exit;
    }
}
//Authenticate
authenticate($user["id"]);
