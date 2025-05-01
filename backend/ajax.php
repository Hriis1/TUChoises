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
}