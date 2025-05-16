<?php
require_once "../header.php";


if (!isset($_GET["id"])) { //if id is not set
    echo '<meta http-equiv="refresh" content="0;url=../index.php">';
    exit;
}

//Try getting the dist
$dist = null;
try {
    $dist = new Distribution($_GET["id"], $mysqli);
} catch (Exception $e) {
    echo '<meta http-equiv="refresh" content="0;url=../index.php">';
    exit;
}


?>