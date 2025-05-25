<?php
require_once "utils/dbUtils.php";

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
            echo json_encode([0, 'name', 'Invalid faculty']);
            exit;
        }

        // Validate uniqueness for 'name' outide of current faculty
        $r1 = $mysqli->query("SELECT 1 FROM faculties WHERE name = '$name' AND id != $id AND deleted = 0 LIMIT 1");
        if ($r1->num_rows) {
            echo json_encode([0, 'name', 'Name already exists']);
            exit;
        }

        // Validate uniqueness for 'short' outide of current faculty
        $r2 = $mysqli->query("SELECT 1 FROM faculties WHERE short = '$short' AND id != $id AND deleted = 0 LIMIT 1");
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
        $faculty = trim($_POST['faculty']);

        if ($name === '') {
            echo json_encode([0, 'name', 'Name required']);
            exit;
        }
        if ($short === '') {
            echo json_encode([0, 'short', 'Short required']);
            exit;
        }
        if ($faculty === '') {
            echo json_encode([0, 'faculty', 'Faculty required']);
            exit;
        }

        $name = $mysqli->real_escape_string($name);
        $short = $mysqli->real_escape_string($short);

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

        $mysqli->query("INSERT INTO majors (name, short, faculty) VALUES ('$name', '$short', '$faculty')");

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

    // Edit major
    if ($_POST['action'] === 'editMajor') {
        // Get submitted data
        $id = trim($_POST['id']);
        $name = trim($_POST['name']);
        $short = trim($_POST['short']);
        $faculty = trim($_POST['faculty']);

        // Validate not empty
        if ($name === '') {
            echo json_encode([0, 'name', 'Name required']);
            exit;
        }
        if ($short === '') {
            echo json_encode([0, 'short', 'Short required']);
            exit;
        }
        if ($faculty === '') {
            echo json_encode([0, 'faculty', 'Faculty required']);
            exit;
        }

        // Escape
        $id = $mysqli->real_escape_string($id);
        $name = $mysqli->real_escape_string($name);
        $short = $mysqli->real_escape_string($short);
        $faculty = $mysqli->real_escape_string($faculty);

        // Validate id
        if (!is_numeric($id)) {
            echo json_encode([0, 'name', 'Invalid major']);
            exit;
        }

        // Validate uniqueness for 'short' outside of current major
        $r = $mysqli->query("SELECT 1 FROM majors WHERE short = '$short' AND id != $id AND deleted = 0 LIMIT 1");
        if ($r->num_rows) {
            echo json_encode([0, 'short', 'Short already exists']);
            exit;
        }

        // Submit to db
        $mysqli->query("UPDATE majors SET name = '$name', short = '$short', faculty = '$faculty' WHERE id = $id");

        // Success
        if ($mysqli->affected_rows >= 0) {
            // Send an alert
            $_SESSION['alert'] = [
                "type" => "success",
                "text" => "Major edited successfully!"
            ];
            echo json_encode([1, "", ""]);
            exit;
        }

        // Error
        $_SESSION['alert'] = [
            "type" => "danger",
            "text" => "Error Editing Major!"
        ];
        echo json_encode([0, "", ""]);
        exit;
    }

    //Get majors of faculty
    if ($_POST['action'] === 'getMajorsOfFaculty') {
        $dbfac = getFromDBByID("faculties", $_POST["facultyShort"], $mysqli, "short");
        $faculty = new Faculty($dbfac["id"], $mysqli);

        $majors = $faculty->getMajors($mysqli);
        $response = [];
        foreach ($majors as $major) {
            $response[] = [
                'id' => $major->getId(),
                'name' => $major->getName(),
                'short' => $major->getShort(),
                'facultyId' => $major->getFacultyId($mysqli),
            ];
        }
        echo json_encode($response);
        exit;

    }

    //Add distribution
    if ($_POST['action'] === 'addDistribution') {
        $name = trim($_POST['name']);
        $ident = trim($_POST['ident']);
        $semester_applicable = trim($_POST['semester_applicable']);
        $faculty = trim($_POST['faculty']);
        $type = trim($_POST['type']);
        $major = $type == "1" ? trim($_POST['major']) : 0;

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
        if ($major === '' && $type == '1') {
            echo json_encode([0, 'major', 'Major required for subjects']);
            exit;
        }
        if ($faculty === '') {
            echo json_encode([0, 'faculty', 'Faculty required']);
            exit;
        }
        if ($type === '') {
            echo json_encode([0, 'type', 'Type required']);
            exit;
        }

        $name = $mysqli->real_escape_string($name);
        $ident = $mysqli->real_escape_string($ident);
        $faculty = $mysqli->real_escape_string($faculty);
        $major = $mysqli->real_escape_string($major);
        $semester_applicable = (int) $semester_applicable;
        $type = (int) $type;

        $rIdent = $mysqli->query("SELECT 1 FROM distributions WHERE ident='$ident' LIMIT 1");
        if ($rIdent->num_rows) {
            echo json_encode([0, 'ident', 'Ident must be unique']);
            exit;
        }

        $mysqli->query("INSERT INTO distributions (name, ident, semester_applicable, major, faculty, type)  VALUES ('$name', '$ident', $semester_applicable, '$major', '$faculty', $type)");

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

    // Edit distribution
    if ($_POST['action'] === 'editDistribution') {
        // Get submitted data
        $id = trim($_POST['id']);
        $name = trim($_POST['name']);
        $ident = trim($_POST['ident']);
        $faculty = trim($_POST['faculty']);
        $type = trim($_POST['type']);
        $major = $type == "1" ? trim($_POST['major']) : 0;

        // Validate not empty
        if ($name === '') {
            echo json_encode([0, 'name', 'Name required']);
            exit;
        }
        if ($ident === '') {
            echo json_encode([0, 'ident', 'Ident required']);
            exit;
        }
        if ($major === '' && $type == '1') {
            echo json_encode([0, 'major', 'Major required for subjects']);
            exit;
        }
        if ($faculty === '') {
            echo json_encode([0, 'faculty', 'Faculty required']);
            exit;
        }
        if ($type === '') {
            echo json_encode([0, 'type', 'Type required']);
            exit;
        }

        // Escape and cast
        $id = $mysqli->real_escape_string($id);
        $name = $mysqli->real_escape_string($name);
        $ident = $mysqli->real_escape_string($ident);
        $major = $mysqli->real_escape_string($major);
        $faculty = $mysqli->real_escape_string($faculty);
        $type = (int) $type;

        // Validate id
        if (!is_numeric($id)) {
            echo json_encode([0, 'name', 'Invalid distribution']);
            exit;
        }

        // Validate ident uniqueness outside current record
        $r = $mysqli->query("SELECT 1 FROM distributions WHERE ident = '$ident' AND id != $id AND deleted = 0 LIMIT 1");
        if ($r->num_rows) {
            echo json_encode([0, 'ident', 'Ident must be unique']);
            exit;
        }

        // Submit to db
        $mysqli->query("UPDATE distributions SET name = '$name', ident = '$ident', major = '$major', faculty = '$faculty', type = $type WHERE id = $id");

        // Success
        if ($mysqli->affected_rows >= 0) {
            $_SESSION['alert'] = [
                "type" => "success",
                "text" => "Distribution edited successfully!"
            ];
            echo json_encode([1, "", ""]);
            exit;
        }

        // Error
        $_SESSION['alert'] = [
            "type" => "danger",
            "text" => "Error Editing Distribution!"
        ];
        echo json_encode([0, "", ""]);
        exit;
    }

    if ($_POST['action'] == 'toggleDistribution') {
        $dist_id = isset($_POST['id']) ? trim($_POST['id']) : '';
        $active = $_POST["active"] != 0 ? 1 : 0;

        //Validate id
        if ($dist_id === '' || !is_numeric($dist_id)) {
            $_SESSION['alert'] = [
                "type" => "danger",
                "text" => "Invalid distribution!"
            ];
            echo 0;
            exit;
        }

        $dist_id = (int) $dist_id;

        // Submit to db
        $mysqli->query("UPDATE distributions SET active = $active WHERE id = $dist_id");

        // Success
        $response = $active == 1 ? "activated" : "deactivated";
        if ($mysqli->affected_rows == 1) {
            $_SESSION['alert'] = [
                "type" => "success",
                "text" => "Distribution {$response} successfully!"
            ];
            echo 1;
            exit;
        }

        // Error
        $_SESSION['alert'] = [
            "type" => "danger",
            "text" => "Error activating distribution!"
        ];
        echo 0;
        exit;
    }

    // Add distribution choices
    if ($_POST['action'] === 'addChoices') {
        $distId = isset($_POST['distribution']) ? trim($_POST['distribution']) : '';
        $distType = isset($_POST['distType']) ? trim($_POST['distType']) : '';
        $count = isset($_POST['count']) ? trim($_POST['count']) : '';
        $names = isset($_POST['name']) ? $_POST['name'] : [];
        $instructors = isset($_POST['instructor']) ? $_POST['instructor'] : [];
        $descs = isset($_POST['description']) ? $_POST['description'] : [];
        $mins = isset($_POST['min']) ? $_POST['min'] : [];
        $maxs = isset($_POST['max']) ? $_POST['max'] : [];
        $editable = isset($_POST['min_max_editble']) ? $_POST['min_max_editble'] : [];

        // Validate distribution ID
        if ($distId === '' || !ctype_digit($distId) || $distType === '' || !ctype_digit($distType)) {
            echo json_encode([0, 'distribution', 'Invalid distribution']);
            exit;
        }
        $distId = (int) $distId;
        $distType = (int) $distType;

        // Validate count
        if ($count === '' || !ctype_digit($count) || (int) $count < 1 || (int) $count > 10) {
            echo json_encode([0, 'count', 'You can add up to 10 choices at a time']);
            exit;
        }
        $count = (int) $count;

        // Validate arrays length
        if (
            count($names) !== $count || count($instructors) !== $count || count($descs) !== $count
            || count($mins) !== $count || count($maxs) !== $count || count($editable) !== $count
        ) {
            echo json_encode([0, 'count', 'Mismatch between count and submitted fields']);
            exit;
        }

        // Prepare insert
        $stmt = $mysqli->prepare("INSERT INTO distribution_choices
          (`name`, `distribution`, `instructor`, `description`, `type`, `min`, `max`, `min_max_editble`)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            echo json_encode([0, '', 'DB prepare failed']);
            exit;
        }

        // Loop and insert each choice
        for ($i = 0; $i < $count; $i++) {
            $n = trim($names[$i]);
            $ins = (int) $instructors[$i];
            $d = trim($descs[$i]);
            $min = (int) trim($mins[$i]);
            $max = (int) trim($maxs[$i]);
            $edit = (int) $editable[$i] != 0 ? 1 : 0;

            if ($n === '') {
                echo json_encode([0, "name[$i]", "Name required for choice " . ($i + 1)]);
                exit;
            }
            if ($instructors[$i] === '' || !ctype_digit($instructors[$i])) {
                echo json_encode([0, "instructor[$i]", "Instructor required for choice " . ($i + 1)]);
                exit;
            }
            if ($d === '') {
                echo json_encode([0, "description[$i]", "Description required for choice " . ($i + 1)]);
                exit;
            }

            if ($max == 0) {
                echo json_encode([0, "max[$i]", "Max cannot be 0"]);
                exit;
            }

            $stmt->bind_param('siisiiii', $n, $distId, $ins, $d, $distType, $min, $max, $edit);
            if (!$stmt->execute()) {
                // Error
                $_SESSION['alert'] = [
                    "type" => "danger",
                    "text" => "Error inserting choice " . ($i + 1)
                ];
                echo json_encode([0, "", ""]);
                exit;
            }
        }

        $stmt->close();
        $_SESSION['alert'] = [
            "type" => "success",
            "text" => "Distribution choices added successfully!"
        ];
        echo json_encode([1, '', '']);
        exit;
    }

    //Edit choice
    if ($_POST['action'] == 'editChoice') {
        $id = (int) $_POST['id'];
        $dc = new DistributionChoice($id, $mysqli);
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);

        //Min max and editable
        if (isset($_POST["min"]) && isset($_POST["max"])) { //if user had permisions to change min/max
            $min = (int) $_POST["min"];
            $max = (int) $_POST["max"];
        } else { //user didnt have permision to change min/max
            $min = $dc->getMin();
            $max = $dc->getMax();
        }
        if (isset($_POST["min_max_editble"])) { //if user had permisions to change min_max_editble
            $min_max_editble = (int) $_POST["min_max_editble"];
        } else { //user didnt have permision to change min_max_editble
            $min_max_editble = $dc->getMinMaxEditable();
        }


        $choiceObj = null;
        try {
            $choiceObj = new DistributionChoice($id, $mysqli);
        } catch (\Exception $e) {
            echo json_encode([0, 'name', 'Invalid distribution choice']);
            exit;
        }

        if (!$name) {
            echo json_encode([0, 'name', 'Name required']);
            exit;
        }
        if (!$description) {
            echo json_encode([0, 'description', 'Description required']);
            exit;
        }
        if (!$min) {
            echo json_encode([0, 'min', 'Min required']);
            exit;
        }
        if (!$max) {
            echo json_encode([0, 'max', 'Max required']);
            exit;
        }

        // update
        $stmt = $mysqli->prepare(" UPDATE distribution_choices SET name = ?, description = ?, min = ?, max = ?, min_max_editble = ? WHERE id = ?");
        $stmt->bind_param("ssiiii", $name, $description, $min, $max, $min_max_editble, $id);
        $stmt->execute();
        $rows = $stmt->affected_rows;
        $stmt->close();

        if ($rows > 0) {
            $_SESSION['alert'] = ["type" => "success", "text" => "Distribution choice edited successfully!"];
            echo json_encode([1, '', '']);
            exit;
        }

        $_SESSION['alert'] = ["type" => "danger", "text" => "Error editing Distribution choice!"];
        echo json_encode([0, '', '']);
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
        $faculty = isset($_POST['faculty']) ? trim($_POST['faculty']) : '';
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
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode([0, 'email', 'Invalid email format']);
            exit;
        }

        $rU = $mysqli->query("SELECT 1 FROM users WHERE username='" . $mysqli->real_escape_string($username) . "'AND deleted = 0 LIMIT 1");
        if ($rU->num_rows) {
            echo json_encode([0, 'username', 'Username exists']);
            exit;
        }
        $rE = $mysqli->query("SELECT 1 FROM users WHERE email='" . $mysqli->real_escape_string($email) . "'AND deleted = 0 LIMIT 1");
        if ($rE->num_rows) {
            echo json_encode([0, 'email', 'Email exists']);
            exit;
        }

        if ($role === '1') {
            if ($fn === '') {
                echo json_encode([0, 'fn', 'Faculty Number required']);
                exit;
            }
            $rFN = $mysqli->query("SELECT 1 FROM users WHERE fn='" . $mysqli->real_escape_string($fn) . "' AND role = 1 AND deleted = 0 LIMIT 1");
            if ($rFN->num_rows) {
                echo json_encode([0, 'fn', 'Faculty Number exists']);
                exit;
            }
            if ($major === '' || $start_year === '') {
                $fld = $major === '' ? 'major' : 'start_year';
                echo json_encode([0, $fld, 'Required for student']);
                exit;
            }
            $fn = $mysqli->real_escape_string($fn);
            $majorObj = null;
            try {
                $majorArr = getFromDBByID("majors", $major, $mysqli, "short");
                $majorObj = new Major($majorArr["id"], $mysqli);
            } catch (\Exception $e) {
                echo json_encode([0, 'major', 'Invalid major']);
            }
            $faculty = $majorObj->getFacultyShort();
            $start_year = (int) $start_year;
            $password = '';
            $active = 0;
        } else {
            if ($faculty === '') {
                echo json_encode([0, 'faculty', 'Faculty required']);
                exit;
            }
            if ($pass === '') {
                echo json_encode([0, 'pass', 'Password required']);
                exit;
            }
            $major = 0;
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
            (username,names,email,pass,role,fn,major,faculty,start_year,active) 
          VALUES 
            ('$uEsc','$nEsc','$eEsc','{$password}','{$role}','{$fn}','{$major}','{$faculty}',{$start_year},{$active})
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

    // Edit user
    if ($_POST['action'] === 'editUser') {
        // get submitted
        $id = trim($_POST['id']);
        $username = trim($_POST['username']);
        $names = trim($_POST['names']);
        $email = trim($_POST['email']);
        $fn = isset($_POST['fn']) ? trim($_POST['fn']) : '';
        $major = isset($_POST['major']) ? trim($_POST['major']) : '';
        $faculty = isset($_POST['faculty']) ? trim($_POST['faculty']) : '';
        $start_year = isset($_POST['start_year']) ? trim($_POST['start_year']) : '';

        // basic
        if (!is_numeric($id)) {
            echo json_encode([0, 'name', 'Invalid user']);
            exit;
        }

        $userObj = null;
        try {
            $userObj = new User($id, $mysqli);
        } catch (\Exception $e) {
            echo json_encode([0, 'name', 'Invalid user']);
        }
        $role = $userObj->getRole();

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
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode([0, 'email', 'Invalid email format']);
            exit;
        }

        // unique
        $uQ = $mysqli->query("SELECT 1 FROM users WHERE username='" . $mysqli->real_escape_string($username) . "' AND id != $id AND deleted = 0");
        if ($uQ->num_rows) {
            echo json_encode([0, 'username', 'Username exists']);
            exit;
        }
        $eQ = $mysqli->query("SELECT 1 FROM users WHERE email='" . $mysqli->real_escape_string($email) . "' AND id != $id AND deleted = 0");
        if ($eQ->num_rows) {
            echo json_encode([0, 'email', 'Email exists']);
            exit;
        }

        // student requires fn & start_year
        if ($role == 1) {
            if ($fn === '') {
                echo json_encode([0, 'fn', 'Faculty Number required']);
                exit;
            }
            $rFN = $mysqli->query("SELECT 1 FROM users WHERE fn='" . $mysqli->real_escape_string($fn) . "' AND role = 1 AND id != $id AND deleted = 0 LIMIT 1");
            if ($rFN->num_rows) {
                echo json_encode([0, 'fn', 'Faculty Number exists']);
                exit;
            }
            if ($start_year === '') {
                echo json_encode([0, 'start_year', 'Start Year required']);
                exit;
            }
            $fn = $mysqli->real_escape_string($fn);
            $start_year = (int) $start_year;

            $majorObj = null;
            try {
                $majorArr = getFromDBByID("majors", $major, $mysqli, "short");
                $majorObj = new Major($majorArr["id"], $mysqli);
            } catch (\Exception $e) {
                echo json_encode([0, 'major', 'Invalid major']);
            }
            $faculty = $majorObj->getFacultyShort();
        } else {
            // teacher: empty fn, start_year
            $fn = '';
            $start_year = 'NULL';

            if ($faculty === '') {
                echo json_encode([0, 'faculty', 'Faculty required']);
                exit;
            }

            $major = "0";
        }

        // preserve pass & active
        $res = $mysqli->query("SELECT pass,active FROM users WHERE id=$id");
        $row = $res->fetch_assoc();
        $pass = $row['pass'];
        $active = $row['active'];

        // escape & cast
        $uEsc = $mysqli->real_escape_string($username);
        $nEsc = $mysqli->real_escape_string($names);
        $eEsc = $mysqli->real_escape_string($email);
        $major = $mysqli->real_escape_string($major);
        $faculty = $mysqli->real_escape_string($faculty);

        // update
        $mysqli->query("
        UPDATE users SET
        role = '$role',
        username = '$uEsc',
        names = '$nEsc',
        email = '$eEsc',
        fn = '$fn',
        major = '$major',
        faculty = '$faculty',
        start_year = $start_year,
        pass = '$pass',
        active = $active
        WHERE id = $id
        ");

        if ($mysqli->affected_rows >= 0) {
            $_SESSION['alert'] = ["type" => "success", "text" => "User edited successfully!"];
            echo json_encode([1, '', '']);
            exit;
        }

        $_SESSION['alert'] = ["type" => "danger", "text" => "Error Editing User!"];
        echo json_encode([0, '', '']);
        exit;
    }

    if ($_POST['action'] === 'addStudentGrades') {
        $count = (int) $_POST["count"];
        $student_id = (int) $_POST["student"];
        $grades = $_POST["grades"];
        $semesters = $_POST["semesters"];

        if (!$count) {
            echo json_encode([0, 'count', 'Error with number of grades']);
            exit;
        }
        if ($count != count($grades)) { //check if count matches grades
            echo json_encode([0, 'count', "Number of grades doesn't match grades"]);
            exit;
        }
        if ($count != count(array_unique($semesters))) { //check if count amtches unique semesters
            echo json_encode([0, 'count', "Number of grades doesn't match unique semesters"]);
            exit;
        }

        $studentCheck = getFromDBCondition("users", "WHERE id = $student_id AND deleted = 0", $mysqli);
        if (!$student_id) {
            echo json_encode([0, 'student', 'Error with student']);
            exit;
        }
        if (count($studentCheck) != 1) {
            echo json_encode([0, 'student', "Student doesn't exist"]);
            exit;
        }

        $mysqli->begin_transaction();
        try {
            $stmt = $mysqli->prepare("INSERT INTO student_grades (user_id, grade, semester) VALUES (?, ?, ?)");

            for ($i = 0; $i < $count; $i++) {
                $grade = round((double) $grades[$i], 2);
                $semester = (int) $semesters[$i];

                //check if grade already included for this semester
                $gradeExists = getFromDBCondition("student_grades", "WHERE user_id = $student_id AND semester = $semester AND deleted = 0", $mysqli);
                if ($gradeExists) {
                    $mysqli->rollback();
                    echo json_encode([0, 'grade-' . $i, 'Grade already added for semester ' . $semester]);
                    exit;
                }

                if ($grade < 2 || $grade > 6) {
                    $mysqli->rollback();
                    echo json_encode([0, 'grade-' . $i, 'Grade must be between 2 and 6']);
                    exit;
                }

                if ($semester < 1 || $semester > 10) {
                    $mysqli->rollback();
                    echo json_encode([0, 'semester-' . $i, 'Semester not valid']);
                    exit;
                }

                $stmt->bind_param("idi", $student_id, $grade, $semester);
                $stmt->execute();
            }

            $stmt->close();
            $mysqli->commit();

            if ($mysqli->affected_rows >= 0) {
                $_SESSION['alert'] = ["type" => "success", "text" => "Grades added successfully"];
                echo json_encode([1, '', '']);
                exit;
            }

            $_SESSION['alert'] = ["type" => "danger", "text" => "Error adding grades!"];
            echo json_encode([0, '', '']);
            exit;

        } catch (Exception $e) {
            //Mysql error
            $mysqli->rollback();
            $_SESSION['alert'] = ["type" => "danger", "text" => "Error adding grades!"];
            echo json_encode([0, '', '']);
        }
        exit;
    }


}