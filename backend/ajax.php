<?php
require_once "config/sessionConfig.php";
require_once "config/dbConfig.php";

require_once "majors/Faculty.php";
require_once "majors/Major.php";

require_once "distributions/Distribution.php";
require_once "distributions/DistributionChoise.php";

require_once "users/User.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //Add faculty
    if ($_POST['action'] === 'addFaculty') {
        //Get the submitted data
        $name = trim($_POST['name']);
        $short = trim($_POST['short']);

        //Validate that they are not empty
        if ($name === '') {
            echo json_encode([0, 'name', 'Name required']);
            exit;
        }
        if ($short === '') {
            echo json_encode([0, 'short', 'Short required']);
            exit;
        }

        //Escape
        $name = $mysqli->real_escape_string($name);
        $short = $mysqli->real_escape_string($short);

        //Validate uniqueness
        $r1 = $mysqli->query("SELECT 1 FROM faculties WHERE name='$name' LIMIT 1");
        if ($r1->num_rows) {
            echo json_encode([0, 'name', 'Name already exists']);
            exit;
        }
        $r2 = $mysqli->query("SELECT 1 FROM faculties WHERE short='$short' LIMIT 1");
        if ($r2->num_rows) {
            echo json_encode([0, 'short', 'Short already exists']);
            exit;
        }

        //Submit to db
        $mysqli->query("INSERT INTO faculties (name, short) VALUES ('$name', '$short')");

        //Success
        if ($mysqli->affected_rows === 1) {

            //Send an alert
            $_SESSION['alert'] = [
                "type" => "success",
                "text" => "Faculty added successfully!"
            ];

            //Echo success
            echo json_encode([1, "", ""]);
            exit;
        }

        //Error
        //Send an alert
        $_SESSION['alert'] = [
            "type" => "danger",
            "text" => "Error Adding Faculty!"
        ];
        echo json_encode([0, "", ""]);
        exit;
    }

    //Edit faculty
    if ($_POST['action'] === 'editFaculty') {
        //Get the submitted data
        $id = trim($_POST["id"]);
        $name = trim($_POST['name']);
        $short = trim($_POST['short']);

        //Validate that they are not empty
        if ($name === '') {
            echo json_encode([0, 'name', 'Name required']);
            exit;
        }
        if ($short === '') {
            echo json_encode([0, 'short', 'Short required']);
            exit;
        }

        //Escape
        $id = $mysqli->real_escape_string($id);
        $name = $mysqli->real_escape_string($name);
        $short = $mysqli->real_escape_string($short);

        //Validate id
        if (!is_numeric($id)) {
            echo json_encode([0, '', 'Invalid faculty ID']);
            exit;
        }

        // Validate uniqueness for 'name' outide of current faculty
        $r1 = $mysqli->query("SELECT 1 FROM faculties WHERE name = '$name' AND id != $id LIMIT 1");
        if ($r1->num_rows) {
            echo json_encode([0, 'name', 'Name already exists']);
            exit;
        }

        // Validate uniqueness for 'short' outide of current faculty
        $r2 = $mysqli->query("SELECT 1 FROM faculties WHERE short = '$short' AND id != $id LIMIT 1");
        if ($r2->num_rows) {
            echo json_encode([0, 'short', 'Short already exists']);
            exit;
        }

        //Submit to db
        $mysqli->query("UPDATE faculties SET name = '$name', short = '$short' WHERE id = $id");

        //Success
        if ($mysqli->affected_rows >= 0) {

            //Send an alert
            $_SESSION['alert'] = [
                "type" => "success",
                "text" => "Faculty edited successfully!"
            ];

            //Echo success
            echo json_encode([1, "", ""]);
            exit;
        }

        //Error
        //Send an alert
        $_SESSION['alert'] = [
            "type" => "danger",
            "text" => "Error Editing Faculty!"
        ];
        echo json_encode([0, "", ""]);
        exit;
    }

    //Add major
    if ($_POST['action'] === 'addMajor') {
        $name = trim($_POST['name']);
        $short = trim($_POST['short']);
        $faculty_id = trim($_POST['faculty_id']);

        if ($name === '') {
            echo json_encode([0, 'name', 'Name required']);
            exit;
        }
        if ($short === '') {
            echo json_encode([0, 'short', 'Short required']);
            exit;
        }
        if ($faculty_id === '') {
            echo json_encode([0, 'faculty_id', 'Faculty required']);
            exit;
        }

        $name = $mysqli->real_escape_string($name);
        $short = $mysqli->real_escape_string($short);
        $faculty_id = (int) $faculty_id;

        $r1 = $mysqli->query("SELECT 1 FROM majors WHERE name='$name' LIMIT 1");
        if ($r1->num_rows) {
            echo json_encode([0, 'name', 'Name already exists']);
            exit;
        }
        $r2 = $mysqli->query("SELECT 1 FROM majors WHERE short='$short' LIMIT 1");
        if ($r2->num_rows) {
            echo json_encode([0, 'short', 'Short already exists']);
            exit;
        }

        $mysqli->query("INSERT INTO majors (name, short, faculty) VALUES ('$name', '$short', $faculty_id)");

        if ($mysqli->affected_rows === 1) {

            //Send an alert
            $_SESSION['alert'] = [
                "type" => "success",
                "text" => "Major added successfully!"
            ];

            //Echo success
            echo json_encode([1, "", ""]);
            exit;
        }

        //Error
        //Send an alert
        $_SESSION['alert'] = [
            "type" => "danger",
            "text" => "Error Adding Major!"
        ];
        echo json_encode([0, "", ""]);
        exit;
    }

    //Add distribution
    if ($_POST['action'] === 'addDistribution') {
        $name = trim($_POST['name']);
        $ident = trim($_POST['ident']);
        $semester_applicable = trim($_POST['semester_applicable']);
        $major = trim($_POST['major']);
        $type = trim($_POST['type']);

        if ($name === '') {
            echo json_encode([0, 'name', 'Name required']);
            exit;
        }
        if ($ident === '') {
            echo json_encode([0, 'ident', 'Ident required']);
            exit;
        }
        if ($semester_applicable === '') {
            echo json_encode([0, 'semester_applicable', 'Year required']);
            exit;
        }
        if (!ctype_digit($semester_applicable) || (int) $semester_applicable < 1 || (int) $semester_applicable > 10) {
            echo json_encode([0, 'semester_applicable', 'Year must be an integer between 1 and 5']);
            exit;
        }
        if ($major === '') {
            echo json_encode([0, 'major', 'Major required']);
            exit;
        }
        if ($type === '') {
            echo json_encode([0, 'type', 'Type required']);
            exit;
        }

        $name = $mysqli->real_escape_string($name);
        $ident = $mysqli->real_escape_string($ident);
        $semester_applicable = (int) $semester_applicable;
        $major = (int) $major;
        $type = (int) $type;

        $rIdent = $mysqli->query("SELECT 1 FROM distributions WHERE ident='$ident' LIMIT 1");
        if ($rIdent->num_rows) {
            echo json_encode([0, 'ident', 'Ident must be unique']);
            exit;
        }

        $mysqli->query("INSERT INTO distributions (name, ident, semester_applicable, major, type)  VALUES ('$name', '$ident', $semester_applicable, $major, $type)");

        if ($mysqli->affected_rows === 1) {

            //Send an alert
            $_SESSION['alert'] = [
                "type" => "success",
                "text" => "Distribution added successfully!"
            ];

            //Echo success
            echo json_encode([1, "", ""]);
            exit;
        }

        //Error
        //Send an alert
        $_SESSION['alert'] = [
            "type" => "danger",
            "text" => "Error Adding Distribution!"
        ];
        echo json_encode([0, "", ""]);
        exit;
    }

    //Add user
    if ($_POST['action'] === 'addUser') {
        $role = trim($_POST['role']);
        $username = trim($_POST['username']);
        $names = trim($_POST['names']);
        $email = trim($_POST['email']);
        $pass = isset($_POST['pass']) ? trim($_POST['pass']) : '';
        $fn = isset($_POST['fn']) ? trim($_POST['fn']) : '';
        $major = isset($_POST['major']) ? trim($_POST['major']) : '';
        $start_year = isset($_POST['start_year']) ? trim($_POST['start_year']) : '';

        if (!in_array($role, ['1', '2'])) {
            echo json_encode([0, 'role', 'Invalid role']);
            exit;
        }
        if ($username === '') {
            echo json_encode([0, 'username', 'Username required']);
            exit;
        }
        if ($names === '') {
            echo json_encode([0, 'names', 'Names required']);
            exit;
        }
        if ($email === '') {
            echo json_encode([0, 'email', 'Email required']);
            exit;
        }

        $rU = $mysqli->query("SELECT 1 FROM users WHERE username='" . $mysqli->real_escape_string($username) . "' LIMIT 1");
        if ($rU->num_rows) {
            echo json_encode([0, 'username', 'Username exists']);
            exit;
        }
        $rE = $mysqli->query("SELECT 1 FROM users WHERE email='" . $mysqli->real_escape_string($email) . "' LIMIT 1");
        if ($rE->num_rows) {
            echo json_encode([0, 'email', 'Email exists']);
            exit;
        }

        if ($role === '1') {
            if ($fn === '') {
                echo json_encode([0, 'fn', 'Faculty Number required']);
                exit;
            }
            if ($major === '' || $start_year === '') {
                $fld = $major === '' ? 'major' : 'start_year';
                echo json_encode([0, $fld, 'Required for student']);
                exit;
            }
            $fn = $mysqli->real_escape_string($fn);
            $major = (int) $major;
            $start_year = (int) $start_year;
            $password = '';
            $active = 0;
        } else {
            if ($pass === '') {
                echo json_encode([0, 'pass', 'Password required']);
                exit;
            }
            $password = password_hash($pass, PASSWORD_BCRYPT);
            $fn = '';
            $start_year = 'NULL';
            $active = 1;
        }

        $uEsc = $mysqli->real_escape_string($username);
        $nEsc = $mysqli->real_escape_string($names);
        $eEsc = $mysqli->real_escape_string($email);

        $mysqli->query("
          INSERT INTO users 
            (username,names,email,pass,role,fn,major,start_year,active) 
          VALUES 
            ('$uEsc','$nEsc','$eEsc','{$password}','{$role}','{$fn}',{$major},{$start_year},{$active})
        ");

        if ($mysqli->affected_rows === 1) {

            //Send an alert
            $_SESSION['alert'] = [
                "type" => "success",
                "text" => "User added successfully!"
            ];

            //Echo success
            echo json_encode([1, "", ""]);
            exit;
        }

        //Error
        //Send an alert
        $_SESSION['alert'] = [
            "type" => "danger",
            "text" => "Error Adding User!"
        ];
        echo json_encode([0, "", ""]);
        exit;
    }
}