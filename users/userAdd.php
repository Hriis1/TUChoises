<?php
require_once "../header.php";

//if user is not admin
if ($user->getRole() != 3) {
    header("Location: ../index.php");
    exit;
}
?>

<main>
    
</main>