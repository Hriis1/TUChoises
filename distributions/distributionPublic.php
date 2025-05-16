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

if (!$dist->canView($user, $mysqli)) { //if user cannot view the distribution
    echo '<meta http-equiv="refresh" content="0;url=../index.php">';
    exit;
}

$choices = $dist->getChoices($mysqli);
?>

<main>
    <div class="container-fluid d-flex flex-column align-items-center pb-5 pt-5">
        <div class="basic-container bg-white bg-opacity-50 p-5 rounded-5 shadow">
            <h2 class="mb-3"><?= htmlspecialchars($dist->getName()) ?></h2>
            <p class="mb-4 text-secondary">
                <?= htmlspecialchars($dist->getFacultyShort()) ?>
                <?php if ($dist->getMajorShort() !== '0') { ?>
                    &bull; <?= htmlspecialchars($dist->getMajorShort()) ?>
                <?php } ?>
                &bull; <?= htmlspecialchars($dist->getTypeText()) ?>
            </p>
            <hr>
            <h3 class="mb-3">Избори</h3>
            <?php foreach ($choices as $choice) { ?>
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($choice->getName()) ?></h5>
                        <h6 class="card-subtitle mb-2 text-muted">Instructor ID:
                            <?= htmlspecialchars($choice->getInstructorId()) ?>
                        </h6>
                        <p class="card-text"><?= nl2br(htmlspecialchars($choice->getDescription())) ?></p>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</main>

<?php
require_once "../footer.php";
?>