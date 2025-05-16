<?php
require_once "../header.php";

//If user is hot user or teacher or condition is not set
if ($user->getRole() != 1 || $user->getRole() != 2 || !isset($_GET["condition"])) {
    echo "Only for students/teachers!";
    require_once "../footer.php";
    exit;
}

//Base condition
$condition = "WHERE deleted = 0 AND ";

if ($_GET["condition"] == "all") {

}


//Condition builder types
$conditionTypes = ["active", "inactive", "chosen", "to_chose", "part"]
    ?>
<?php
require_once "../sidebar.php";
?>
<div class="container-fluid d-flex flex-column align-items-center pb-5 pt-5">
    <div class="user-container bg-white bg-opacity-50 p-5 rounded-5 shadow">
        <div class="row text-center">
            <div class="col-6">zaza</div>
            <div class="col-6">waza</div>
        </div>
        <div class="row text-center">
            <div class="col">111</div>
        </div>
    </div>
</div>
</main>

<?php
require_once "../footer.php";
?>