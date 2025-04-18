<?php

$mysqli = null;

try {
    $host = "localhost";
    $dbname = "tu_choices";
    $dbusername = "root";
    $dbpassword = "";

    $mysqli = new mysqli($host, $dbusername, $dbpassword, $dbname);
    if ($mysqli->connect_errno) {
        throw new Exception("Failed to connect to MySQL: " . $mysqli->connect_error);
    }
    mysqli_query($mysqli, "SET NAMES 'UTF8'");

} catch (mysqli_sql_exception $e) {
    die("MySQL error: " . $e->getMessage());
} catch (Exception $e) {
    die("General error: " . $e->getMessage());
}

// Report all mysqli errors as exceptions
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
