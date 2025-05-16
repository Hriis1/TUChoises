<?php
require_once "../header.php";

$userID = $user->getId();
$role = $user->getRole();

//If user is hot user or teacher or condition is not set
if (($role != 1 && $role != 2) || !isset($_GET["condition"])) {
    header("Location: ../index.php");
    exit;
}

//Base condition
$condition = "WHERE deleted = 0 AND ";
if ($role == 1) { //student
} else { //teacher
    $condition .= "id IN (SELECT distribution FROM distribution_choices WHERE instructor = $userID)";
}

//Based on get param
$headerText = "";
if ($_GET["condition"] == "all") {
    $headerText = "All distributions";
} else if ($_GET["condition"] == "active") {
    $headerText = "Active distributions";
    $condition .= " AND active = 1";
} else if ($_GET["condition"] == "inactive") {
    $headerText = "Inactive distributions";
    $condition .= " AND active = 0";
} else if ($_GET["condition"] == "to_make") {
    $headerText = "Choice needed";
    if ($role == 1) {
        $condition .= " AND active = 1 AND NOT EXISTS (SELECT 1 FROM s_d_choices WHERE user_id = $userID AND distribution_id = distributions.id)";
    }
} else if ($_GET["condition"] == "chosen") {
    $headerText = "Choice made";
    if ($role == 1) {
        $condition .= " AND EXISTS (SELECT 1 FROM s_d_choices  WHERE user_id = $userID  AND distribution_id = distributions.id)";
    }
}

//Build the final query
$distributions = getFromDBCondition("distributions", $condition, $mysqli);

?>
<?php
require_once "../sidebar.php";
?>
<div class="container-fluid d-flex flex-column align-items-center pb-5 pt-5">
    <div class="user-container bg-white bg-opacity-50 p-5 rounded-5 shadow">
        <h1 class="text-center mb-4"><?= $headerText ?></h1>
        <div class="row">
            <?php foreach ($distributions as $dist) { ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card shadow-sm rounded-4" style="height: 230px;">
                        <div class="card-body d-flex flex-column p-3">
                            <div class="flex-grow-1 d-flex justify-content-center align-items-center text-center">
                                <h5 class="card-title mb-0"><?= htmlspecialchars($dist['name']) ?></h5>
                            </div>
                            <a href="distributionPublic.php?id=<?= $dist['id'] ?>" class="btn btn-primary w-100 mt-auto">
                                View
                            </a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

    </div>
</div>
</main>

<?php
require_once "../footer.php";
?>