<?php
require_once "../header.php";


if (!isset($_GET["id"])) { //if id is not set
    echo '<meta http-equiv="refresh" content="0;url=../index.php">';
    exit;
}

//Try getting the dist
$dist = null;
$faculty = null;
$major = null;
try {
    $dist = new Distribution($_GET["id"], $mysqli);
    $faculty = new Faculty($dist->getFacultyID($mysqli), $mysqli);
    if ($dist->getType() == 1) { //if dist is izbiraema disciplina
        $major = new Major($dist->getMajorID($mysqli), $mysqli);
    }
} catch (Exception $e) {
    echo '<meta http-equiv="refresh" content="0;url=../index.php">';
    exit;
}

if (!$dist->canView($user, $mysqli)) { //if user cannot view the distribution
    echo '<meta http-equiv="refresh" content="0;url=../index.php">';
    exit;
}

$choices = $dist->getChoices($mysqli);
?>

<style>
    .distribution-head-container {
        display: flex;
    }

    .choices-container {}

    @media only screen and (max-width: 768px) {
        .distribution-head-container {
            display: block;
            margin-bottom: 10px;
        }

        .public-badge {
            margin-left: -20px;
        }
    }
</style>
<?php
require_once "../sidebar.php";
?>
<main>
    <div class="container-fluid d-flex flex-column align-items-center pb-5 pt-5">
        <div class="basic-container relative bg-white bg-opacity-50 p-5 rounded-5 shadow"
            style="padding-bottom: 75px !important;">
            <div class="distribution-head-container">
                <h2 class="mb-3"><?= htmlspecialchars($dist->getName()) ?></h2> &nbsp; &nbsp;
                <?php if ($dist->isActive()) { ?>
                    <span style="height: 25px;" class="badge bg-success mt-2 public-badge">Active</span>
                <?php } else { ?>
                    <span style="height: 25px;" class="badge bg-danger mt-2 public-badge">Inactive</span>
                <?php } ?>
            </div>
            <p class="mb-4 text-secondary">
                <?= htmlspecialchars($faculty->getName()) ?>
                <?php if ($major) { ?>
                    &bull; <?= htmlspecialchars($major->getName()) ?>
                <?php } ?>
                &bull; <?= htmlspecialchars($dist->getTypeText()) ?>
            </p>
            <hr>
            <h3 class="mb-3">Избори</h3>
            <div class="choices-container">
                <?php foreach ($choices as $choice) {
                    $currInstructor = new User($choice->getInstructorId(), $mysqli);
                    ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($choice->getName()) ?></h5>
                            <h6 class="card-subtitle mb-2 text-muted">Instructor:
                                <?= htmlspecialchars($currInstructor->getNames()) ?>
                            </h6>
                            <p class="card-text"><?= nl2br(htmlspecialchars($choice->getDescription())) ?></p>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="position-absolute bottom-0 start-0 w-100 p-4 text-end">
                <?php
                $studentPermisions = $dist->getStudentPermisions($user, $mysqli);
                $distID = $dist->getId();
                if ($studentPermisions == 1) { //can choose ?>
                    <a href="distributionMakeChoice.php?id=<?= $distID ?>" class="btn btn-success">Make Choice</a>
                <?php } else if ($studentPermisions == 2) { //can view choice made ?>
                        <a href="distributionViewChoice.php?id=<?= $distID ?>&userID=<?= $user->getId(); ?>"
                            class="btn btn-success">View My Choice</a>
                <?php } else if ($dist->canTeacherEditChoice($user, $mysqli)) { //teacher can edit their choice
                    $choiceID = 0;
                    foreach ($choices as $curr) {
                        if ($curr->getInstructorId() == $user->getId()) {
                            $choiceID = $curr->getId();
                            break;
                        }
                    } ?>
                            <a href="distributionChoiceEdit.php?id=<?= $choiceID ?>" class="btn btn-success">Edit My Entry</a>
                <?php } ?>

            </div>
        </div>
    </div>
</main>

<?php
require_once "../footer.php";
?>