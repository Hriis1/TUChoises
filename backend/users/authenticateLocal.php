<?php

header('Content-Type: application/json');
require_once "../config/dbConfig.php";
require_once "../config/sessionConfig.php";

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

if ($user['role'] == 1) { //check if user is student
    if ($user['active'] != 1) { //if users acc is not activated
        // TODO: handle student with not active acc
        exit;
    }
}

//Check the password
if (!password_verify($pass, $user['pass'])) {
    echo json_encode([0, 'pass', 'Invalid password']);
    exit;
}

//Authenticate
authenticate(1);
