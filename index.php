<?php
require_once "header.php";

//Redirect based on user type
$role = $user->getRole();
if ($role == 1 || $role == 2) { //if user is student or teacher
    //Redirect to all distributions
    header("Location: distributions/myDistributions.php?condition=all");
    exit;
} else if ($role == 3) { //if user is admin
    //Redirect to admin panel
    header("Location: admin/adminPanel.php");
    exit;
}
?>

<main>
    <?php
    require_once "sidebar.php";
    ?>
    <div class="container">
    </div>
</main>

<?php
require_once "footer.php";
?>