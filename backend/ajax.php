<?php
require_once "config/sessionConfig.php";
require_once "config/dbConfig.php";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($_POST['action'] === 'addFaculty') {
        //Get the submitted data
        $name = trim($_POST['name']);
        $short = trim($_POST['short']);

        //Valiedate that they are not empty
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
            echo json_encode([1, "", ""]);
            exit;
        }

        //Error
        echo json_encode([0, "", "Error Adding Faculty"]);
        exit;
    }

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
            echo json_encode([1, "", ""]);
            exit;
        }

        echo json_encode([0, "", "Error Adding Major"]);
        exit;
    }

    if ($_POST['action'] === 'addDistribution') {
        $name = trim($_POST['name']);
        $ident = trim($_POST['ident']);
        $year_applicable = trim($_POST['year_applicable']);
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
        if ($year_applicable === '') {
            echo json_encode([0, 'year_applicable', 'Year required']);
            exit;
        }
        if (!ctype_digit($year_applicable) || (int) $year_applicable < 1 || (int) $year_applicable > 5) {
            echo json_encode([0, 'year_applicable', 'Year must be an integer between 1 and 5']);
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
        $year_applicable = (int) $year_applicable;
        $major = (int) $major;
        $type = (int) $type;

        $rIdent = $mysqli->query("SELECT 1 FROM distributions WHERE ident='$ident' LIMIT 1");
        if ($rIdent->num_rows) {
            echo json_encode([0, 'ident', 'Ident must be unique']);
            exit;
        }

        $mysqli->query("INSERT INTO distributions (name, ident, year_applicable, major, type)  VALUES ('$name', '$ident', $year_applicable, $major, $type)");

        if ($mysqli->affected_rows === 1) {
            echo json_encode([1, "", ""]);
            exit;
        }

        echo json_encode([0, "", "Error Adding Distribution"]);
        exit;
    }


}