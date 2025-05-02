<?php
require_once "../config/sessionConfig.php";

unset($_SESSION["userID"]);
header('Location: ../../index.php');
exit;
?>