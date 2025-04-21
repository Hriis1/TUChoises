<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') { //if req is POST
    if (isset($_POST["action"]) && $_POST["action"] == "loginUser") {

        // 1. Grab form input (sanitize in real code!)
        $username = $_POST['username'];
        $password = $_POST['pass'];

        echo $username . " " . $password;
    } else {
        echo "Unrecognized access :(";
    }
} else {
    echo "Only POST allowed :(";
}