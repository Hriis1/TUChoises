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

if ($dist->getStudentPermisions($user, $mysqli) != 1) { //if user cannot choose for the distribution
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

    .star-rating {
        display: flex;
        flex-direction: row-reverse;
        font-size: 3rem;
    }

    .star-rating input {
        display: none;
    }

    .star-rating label {
        cursor: pointer;
        line-height: 1;
        padding: 0 0.2rem;
    }

    .star-rating input:checked~label,
    .star-rating label:hover,
    .star-rating label:hover~label {
        color: gold;
    }

    .card-body .info {
        flex: 1;
    }

    .card-body .star-rating {
        align-self: center;
    }

    @media only screen and (max-width: 768px) {
        .distribution-head-container {
            display: block;
            margin-bottom: 10px;
        }

        .card-body {
            flex-direction: row;
        }

        .star-rating {
            flex-direction: column-reverse !important;
            margin-left: 1rem;
        }

        .star-rating label {
            display: block;
            padding: 0.2rem 0 !important;
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
                <h2 class="mb-3">Choose for <?= htmlspecialchars($dist->getName()) ?></h2> &nbsp; &nbsp;
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
            <form novalidate method="POST" action="../backend/distributions/makeChoiceBE.php">
                <input type="hidden" name="action" value="makeChoice">
                <input type="hidden" name="userID" value="<?= $user->getId(); ?>">
                <input type="hidden" name="distID" value="<?= $dist->getId(); ?>">
                <div class="choices-container">
                    <?php foreach ($choices as $choice):
                        $cid = $choice->getId();
                        ?>
                        <div class="card mb-4">
                            <div class="card-body d-flex align-items-center">
                                <div class="info">
                                    <h5 class="card-title"><?= htmlspecialchars($choice->getName()) ?></h5>
                                    <h6 class="card-subtitle mb-2 text-muted">
                                        Instructor:
                                        <?= htmlspecialchars((new User($choice->getInstructorId(), $mysqli))->getNames()) ?>
                                    </h6>
                                    <p class="card-text"><?= nl2br(htmlspecialchars($choice->getDescription())) ?></p>
                                </div>
                                <div class="star-rating ms-4">
                                    <?php for ($i = 5; $i >= 1; $i--) { ?>
                                        <input type="radio" id="star-<?= $cid ?>-<?= $i ?>" name="rating[<?= $cid ?>]"
                                            value="<?= $i ?>" required>
                                        <label for="star-<?= $cid ?>-<?= $i ?>">★</label>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="text-end">
                    </div>
                </div>
                <div class="position-absolute bottom-0 start-0 w-100 p-4 text-end">
                    <button type="submit" class="btn btn-primary">Submit Ratings</button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php
require_once "../footer.php";
?>

<script>
    $(function () {
        $('form').on('submit', function (e) {
            e.preventDefault(); // always intercept

            let allRated = true;
            $('.star-rating').each(function () {
                if ($(this).find('input:checked').length === 0) {
                    allRated = false;
                    return false;
                }
            });

            if (!allRated) {
                alert('Please, rate each choice!');
            } else {
                // remove the novalidate to let HTML5 required run
                this.removeAttribute('novalidate');
                this.submit();
            }
        });
    });
</script>