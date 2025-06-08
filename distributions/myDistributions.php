<?php
require_once "../header.php";

$userID = $user->getId();
$role = $user->getRole();
$semester = $user->getSemester();

//If user is not student or teacher or condition is not set
if (($role != 1 && $role != 2) || !isset($_GET["condition"])) {
    header("Location: ../index.php");
    exit;
}

//Base condition
$condition = "WHERE deleted = 0 AND ";
if ($role == 1) { //student
    $majorShort = $user->getMajorShort();
    $facultyShort = $user->getFacultyShort();
    $condition .= " ($semester - semester_applicable) >= -1"; //semester check
    $condition .= " AND ((type = 1 AND major = '$majorShort') OR (type = 2 AND faculty = '$facultyShort'))"; //major/faculty check
} else { //teacher
    $condition .= "id IN (SELECT distribution FROM distribution_choices WHERE instructor = $userID)";
}

//Based on get param
$headerText = "";
if ($_GET["condition"] == "active") {
    $headerText = "All distributions";
    if ($role == 2) {
        $headerText = "Active distributions";
        $condition .= " AND active = 1";
    }
} else if ($_GET["condition"] == "inactive") {
    $headerText = "All distributions";
    if ($role == 2) {
        $headerText = "Inactive distributions";
        $condition .= " AND active = 0";
    }
} else if ($_GET["condition"] == "to_make") {
    $headerText = "All distributions";
    if ($role == 1) {
        $headerText = "Choice needed";
        $condition .= " AND ($semester - semester_applicable) IN (-1 , 0)"; //semester check, if user can make a choice
        $condition .= " AND active = 1 AND NOT EXISTS (SELECT 1 FROM s_d_scores WHERE user_id = $userID AND distribution_id = distributions.id)";
    }
} else if ($_GET["condition"] == "chosen") {
    $headerText = "All distributions";
    if ($role == 1) {
        $headerText = "Choice made";
        $condition .= " AND EXISTS (SELECT 1 FROM s_d_scores  WHERE user_id = $userID  AND distribution_id = distributions.id)";
    }
} else if ($_GET["condition"] == "distributed") {
    $headerText = "All distributions";
    if ($role == 1) {
        $headerText = "Distributted";
        $condition .= " AND EXISTS (SELECT 1 FROM s_d_scores  WHERE user_id = $userID  AND distribution_id = distributions.id) ";
        $condition .= " AND EXISTS (SELECT 1 FROM distributed_students  WHERE student_id = $userID  AND dist_id = distributions.id) ";
    }
} else {
    $headerText = "All distributions";
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