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

        //Order by name
        usort($response, fn($a, $b) => strcmp($a['name'], $b['name']));

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

    if ($_POST['action'] == 'canBeDistributed') {
        $id = isset($_POST["id"]) ? (int) $_POST["id"] : 0;

        $distribution = getFromDBCondition("distributions", "WHERE id = $id AND active = 1 AND deleted = 0", $mysqli);

        if (!$distribution) {
            // Error
            $_SESSION['alert'] = [
                "type" => "danger",
                "text" => "Error Finding Distribution!"
            ];
            echo json_encode([0, "", ""]);
            exit;
        }

        //Error checks
        $distribution = new Distribution($id, $mysqli);
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
        $userCount = count($users);

        $distChoices = getFromDBCondition("distribution_choices", "WHERE distribution = $id and deleted = 0", $mysqli);

        //Check if there are enough students to fulfill the min and not too many to exceed the max
        $minSum = 0;
        $maxSum = 0;
        foreach ($distChoices as $currChoice) {
            $minSum += (int) $currChoice["min"];
            $maxSum += (int) $currChoice["max"];
        }
        if (count($users) < $minSum) {
            echo json_encode([-3, "Minimum of $minSum users needed for this distribution, $userCount students eligible"]);
            exit;
        } else if (count($users) > $maxSum) {
            echo json_encode([-3, "Maximum of $maxSum users needed for this distribution, $userCount students eligible"]);
            exit;
        }

        //check if every student has needed grade
        foreach ($users as $user) {
            $fn = $user['fn'];
            $name = $user['names'];
            $grades = getFromDBCondition(
                "student_grades",
                "WHERE student_fn = '$fn' AND semester = $semester_applicable AND deleted = 0",
                $mysqli
            );
            if (count($grades) == 0) {
                $errorResponse[] = "$name ($fn) doesn't have a grade for semester $semester_applicable";
            } else if (count($grades) > 1) {
                $errorResponse[] = "$name ($fn) has more then 1 grade for $semester_applicable";
            }
        }

        //if there was error with grades return
        if ($errorResponse) {
            echo json_encode([-1, $errorResponse]);
            exit;
        }

        //check if every student has chosen in s_d_scores
        foreach ($users as $user) {
            $user_id = $user['id'];
            $fn = $user['fn'];
            $name = $user['names'];
            $scores = getFromDBCondition(
                "s_d_scores",
                "WHERE user_id = $user_id AND distribution_id = $id AND deleted = 0",
                $mysqli
            );
            if (count($scores) == 0) {
                $errorResponse[] = "$name ($fn) has not yet made a choice";
            }
        }

        if ($errorResponse) {
            echo json_encode([-2, $errorResponse]);
            exit;
        }

        echo json_encode([1, []]);
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
            echo json_encode([0, '', 'Failed to submit to db']);
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
        $student_fn = $_POST["student"];
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

        if (!$student_fn) {
            echo json_encode([0, 'student', 'Error with student']);
            exit;
        }
        $studentCheck = getFromDBCondition("users", "WHERE fn = $student_fn AND deleted = 0", $mysqli);
        if (count($studentCheck) != 1) {
            echo json_encode([0, 'student', "Student doesn't exist"]);
            exit;
        }

        $mysqli->begin_transaction();
        try {
            $stmt = $mysqli->prepare("INSERT INTO student_grades (student_fn, grade, semester) VALUES (?, ?, ?)");

            for ($i = 0; $i < $count; $i++) {
                $grade = round((double) $grades[$i], 2);
                $semester = (int) $semesters[$i];

                //check if grade already included for this semester
                $gradeExists = getFromDBCondition("student_grades", "WHERE student_fn = $student_fn AND semester = $semester AND deleted = 0", $mysqli);
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

                $stmt->bind_param("sdi", $student_fn, $grade, $semester);
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

    if ($_POST["action"] === 'editStudentGrade') {
        $grade_id = isset($_POST["id"]) ? $_POST["id"] : 0;
        $grade = isset($_POST["grade"]) ? (float) $_POST["grade"] : null;

        $gradeDB = getFromDBByID("student_grades", $grade_id, $mysqli);

        //Error check
        if (!$gradeDB) {
            echo json_encode([0, '', 'Invalid grade row']);
            exit;
        }

        if (!$grade || $grade < 2 || $grade > 6) {
            echo json_encode([0, 'grade', 'Invalid grade value']);
            exit;
        }

        //Edit grade in db
        $stmt = $mysqli->prepare("UPDATE student_grades SET grade = ? WHERE id = ?");
        $stmt->bind_param("di", $grade, $grade_id);
        $stmt->execute();

        if ($stmt->affected_rows >= 0) {
            $_SESSION['alert'] = ["type" => "success", "text" => "Grade edited successfully"];
            echo json_encode([1, '', '']);
            exit;
        }

        $_SESSION['alert'] = ["type" => "danger", "text" => "Error editing grade!"];
        echo json_encode([0, '', '']);
        exit;
    }

    if ($_POST['action'] == 'getPossibleDistributions') {
        $data = [];
        $student_id = (int) $_POST['student'];
        try {
            $student = new User($student_id, $mysqli);
        } catch (\Throwable $th) {
            echo json_encode($data);
            exit;
        }

        $st_major = $student->getMajorShort();
        $st_faculty = $student->getFacultyShort();

        $condition = "WHERE deleted = 0 AND ((type = 1 AND major = '$st_major') OR (type = 2 AND faculty = '$st_faculty')) ORDER BY name";
        $dists = getFromDBCondition('distributions', $condition, $mysqli);
        foreach ($dists as $dist) {
            $data[] = [
                'id' => $dist['id'],
                'name' => $dist['name']
            ];
        }
        echo json_encode($data);
        exit;
    }

    if ($_POST['action'] == 'getChoicesForDistribution') {
        $data = [];
        $dist_id = (int) $_POST['distribution'];
        try {
            $distribution = new Distribution($dist_id, $mysqli);
        } catch (\Throwable $th) {
            echo json_encode($data);
            exit;
        }

        $choices = $distribution->getChoices($mysqli);
        foreach ($choices as $choice) {
            $data[] = [
                'id' => $choice->getId(),
                'name' => $choice->getName()
            ];
        }
        echo json_encode($data);
        exit;
    }

    if ($_POST['action'] == 'distributeManually') {
        $student_id = isset($_POST["student"]) ? (int) $_POST["student"] : 0;
        $dist_id = isset($_POST["distribution"]) ? (int) $_POST["distribution"] : 0;
        $choice_id = isset($_POST["choice"]) ? (int) $_POST["choice"] : 0;

        if ($student_id == 0) {
            echo json_encode([0, 'student', 'Invalid student']);
            exit;
        }

        if ($dist_id == 0) {
            echo json_encode([0, 'distribution', 'Invalid distribution']);
            exit;
        }

        if ($choice_id == 0) {
            echo json_encode([0, 'choice', 'Invalid choice']);
            exit;
        }

        //Check if student already was distributed for this dist
        $distributed_check = getFromDBCondition("distributed_students", "WHERE student_id = $student_id AND dist_id = $dist_id AND deleted = 0", $mysqli);
        if ($distributed_check) {
            echo json_encode([0, 'student', 'Student already distributed for this distribution']);
            exit;
        }

        // Prepare insert
        $stmt = $mysqli->prepare("INSERT INTO distributed_students
          (`student_id`, `dist_id`, `dist_choice_id`)
          VALUES (?, ?, ?)");

        $stmt->bind_param('iii', $student_id, $dist_id, $choice_id);
        if (!$stmt->execute()) {
            // Error
            $_SESSION['alert'] = ["type" => "danger", "text" => "Failed to submit to db"];
            echo json_encode([-1, '', '']);
            exit;
        }

        //Success
        $_SESSION['alert'] = ["type" => "success", "text" => "Student distributed successfully"];
        echo json_encode([1, '', '']);
        exit;
    }

    if ($_POST['action'] == 'editDistributedStudent') {
        $id = isset($_POST["id"]) ? (int) $_POST["id"] : 0;
        $choice_id = isset($_POST["choice"]) ? (int) $_POST["choice"] : 0;

        if ($id == 0) {
            echo json_encode([0, 'id', 'Invalid student distribution row']);
            exit;
        }

        if ($choice_id == 0) {
            echo json_encode([0, 'choice', 'Invalid choice']);
            exit;
        }

        $row = getFromDBByID("distributed_students", $id, $mysqli);
        if (!$row) {
            echo json_encode([0, 'id', 'Distributed row not found']);
            exit;
        }

        $stmt = $mysqli->prepare("UPDATE distributed_students SET dist_choice_id = ? WHERE id = ?");
        $stmt->bind_param('ii', $choice_id, $id);
        if (!$stmt->execute()) {
            $_SESSION['alert'] = ["type" => "danger", "text" => "Failed to update row"];
            echo json_encode([-1, '', '']);
            exit;
        }

        $_SESSION['alert'] = ["type" => "success", "text" => "Student distribution updated successfully"];
        echo json_encode([1, '', '']);
        exit;
    }

    if ($_POST['action'] == 'downloadDistribution') {
        $id = isset($_POST['dist_id']) ? (int) $_POST['dist_id'] : 0;
        $year = isset($_POST['year']) ? (int) $_POST['year'] : 0;

        $dist = getFromDBByID('distributions', $id, $mysqli);
        if (!$dist) {
            // Just print an error page
            echo "<h3>Distribution with id: $id not found</h3>";
            exit;
        }

        $ident = $dist['ident'];
        $studentsData = [];

        // Build condition for year if needed (assuming student_fn or start_year in users table)
        $userYearCondition = '';
        if ($year > 0) {
            $userYearCondition = "AND u.start_year = $year";
        }

        $query = "
            SELECT 
            ds.student_id,
            u.id AS st_id,
            u.names AS student_name,
            u.fn AS student_fn,
            ds.dist_choice_id AS distributed_in_id,
            dc.name AS distributed_in_name,
            dc.instructor AS teacher_id
            FROM distributed_students ds
            LEFT JOIN users u ON ds.student_id = u.id
            LEFT JOIN distribution_choices dc ON ds.dist_choice_id = dc.id
            WHERE ds.deleted = 0 
            AND ds.dist_id = $id
            $userYearCondition";
        $result = $mysqli->query($query);

        while ($row = $result->fetch_assoc()) {
            // get teacher name
            $teacherName = '';
            if ($row['teacher_id']) {
                $teacherRes = getFromDBByID('users', (int) $row['teacher_id'], $mysqli);
                $teacherName = $teacherRes ? $teacherRes['names'] : '';
            }
            $studentsData[] = [
                'student_id' => $row['st_id'],
                'student_name' => $row['student_name'],
                'student_fn' => $row['student_fn'],
                'distributed_in_id' => $row['distributed_in_id'],
                'distributed_in_name' => $row['distributed_in_name'],
                'teacher_name' => $teacherName
            ];
        }

        $data = [
            'distribution_id' => $id,
            'distribution_ident' => $ident,
            'year' => ($year == 0 ? 'All' : $year),
            'data_date' => date('Y-m-d H:i:s'),
            'distributed_students' => $studentsData
        ];


        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="distribution_' . $id . '.json"');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    if ($_POST['action'] == 'downloadUserDistributions') {
        $user_id = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;

        $user = getFromDBByID('users', $user_id, $mysqli);
        if (!$user) {
            echo "<h3>User with id: $user_id not found</h3>";
            exit;
        }

        $user_name = $user['names'];
        $user_fn = $user['fn'];
        $user_start_year = $user['start_year'];

        $query = "
            SELECT
            ds.dist_choice_id AS distributed_in_id,
            dc.name AS distributed_in_name,
            dc.instructor AS teacher_id
            FROM distributed_students ds
            LEFT JOIN distribution_choices dc ON ds.dist_choice_id = dc.id
            WHERE ds.deleted = 0
            AND ds.student_id = $user_id";
        $result = $mysqli->query($query);

        $distributed_at = [];
        while ($row = $result->fetch_assoc()) {
            // get teacher name
            $teacherName = '';
            if ($row['teacher_id']) {
                $teacherRes = getFromDBByID('users', (int) $row['teacher_id'], $mysqli);
                $teacherName = $teacherRes ? $teacherRes['names'] : '';
            }
            $distributed_at[] = [
                'distributed_in_id' => $row['distributed_in_id'],
                'distributed_in_name' => $row['distributed_in_name'],
                'teacher_name' => $teacherName
            ];
        }

        $data = [
            'student_id' => $user_id,
            'student_name' => $user_name,
            'student_fn' => $user_fn,
            'start_year' => $user_start_year,
            'downloaded_at' => date('Y-m-d H:i:s'),
            'distributed_at' => $distributed_at
        ];

        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="user_' . $user_id . '_distributions.json"');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

}

