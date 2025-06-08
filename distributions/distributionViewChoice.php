<?php
require_once "../header.php";
if (!isset($_GET["id"])) {
    echo '<meta http-equiv="refresh" content="0;url=../index.php">';
    exit;
}
try {
    $dist = new Distribution($_GET["id"], $mysqli);
    $distID = $dist->getId();
    $faculty = new Faculty($dist->getFacultyID($mysqli), $mysqli);
    $major = $dist->getType() == 1 ? new Major($dist->getMajorID($mysqli), $mysqli) : null;
    $userGET = new User($_GET["userID"], $mysqli);
} catch (Exception $e) {
    echo '<meta http-equiv="refresh" content="0;url=../index.php">';
    exit;
}

//if the user from get param hasnt chosen yet
if ($dist->getStudentPermisions($userGET, $mysqli) != 2) {
    echo '<meta http-equiv="refresh" content="0;url=../index.php">';
    exit;
}

//Check if curr user can view scores
if ($user->getRole() == 1) { //if user is a student
    if ($user->getId() != $userGET->getId()) { //if ids dont match
        echo '<meta http-equiv="refresh" content="0;url=../index.php">';
        exit;
    }
} else if ($user->getRole() == 2) { //if user is a teacher
    //teachers cannot view choices
    echo '<meta http-equiv="refresh" content="0;url=../index.php">';
    exit;
} //if user is admin they can view

//if user is student and is student and has been distributed
$distributed = false;
if ($user->getRole() == 1) {
    $user_id = $userGET->getId();
    $distributed_check = getFromDBCondition("distributed_students", "WHERE student_id = $user_id AND dist_id = $distID AND deleted = 0", $mysqli);
    if ($distributed_check) {
        $distributed = true;
        $distributed_in = $distributed_check[0]["dist_choice_id"];
    }

}


$ratings = $dist->getStudentRatings($_GET["userID"], $mysqli);

require_once "../sidebar.php";
?>
<main>
    <div class="container-fluid d-flex flex-column align-items-center pb-5 pt-5">
        <div class="basic-container bg-white bg-opacity-50 p-5 rounded-5 shadow" style="padding-bottom:75px">
            <h2 class="mb-3">Ratings of <?= htmlspecialchars($user->getNames()) ?> for
                <?= htmlspecialchars($dist->getName()) ?>
            </h2>
            <p class="mb-4 text-secondary">
                <?= htmlspecialchars($faculty->getName()) ?>
                <?php if ($major) { ?>&bull; <?= htmlspecialchars($major->getName()) ?><?php } ?>
                &bull; <?= htmlspecialchars($dist->getTypeText()) ?>
                <?php if ($distributed) { ?>
                    <span style="height: 25px; position:relative; top:-2px;"
                        class="badge bg-info text-dark ms-2 public-badge">Distributed</span>
                <?php } ?>
            </p>
            <hr>
            <?php foreach ($ratings as $curRating):
                $choice = new DistributionChoice($curRating["choice_id"], $mysqli);
                $r = (int) $curRating["score"];
                $distributed_choice = ($distributed && $curRating["choice_id"] == $distributed_in);
                ?>
                <div class="card mb-4 <?= $distributed_choice ? 'border-success bg-success bg-opacity-10' : '' ?>">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-9 col-md-8 info">
                                <h5 class="card-title"><?= htmlspecialchars($choice->getName()) ?></h5>
                                <h6 class="card-subtitle mb-2 text-muted">
                                    Instructor:
                                    <?= htmlspecialchars((new User($choice->getInstructorId(), $mysqli))->getNames()) ?>
                                </h6>
                                <p class="card-text"><?= nl2br(htmlspecialchars($choice->getDescription())) ?></p>
                            </div>
                            <div
                                class="col-3 col-md-4 star-rating d-flex flex-column flex-md-row justify-content-end align-items-center">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <label class="mb-1 mb-md-0 ms-md-1"
                                        style="color: <?= $r >= $i ? 'gold' : '#ccc' ?>; font-size:2rem;">
                                        ★
                                    </label>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    </div>
</main>
<?php
require_once "../footer.php";
?>