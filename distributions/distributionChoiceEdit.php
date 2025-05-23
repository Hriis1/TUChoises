<?php
require_once "../header.php";

// if there is no get id
if (!isset($_GET["id"])) {
    echo '<meta http-equiv="refresh" content="0;url=../admin/adminPanel.php">';
}

//Try getting the distribution choice, distribution and instructor
$dc = null;
$dist = null;
$instructor = null;
try {
    $dc = new DistributionChoice($_GET["id"], $mysqli);
    $dist = new Distribution($dc->getDistributionId(), $mysqli);
    $instructor = new User($dc->getInstructorId(), $mysqli);
} catch (Exception $e) {
    echo $e->getMessage();
    require_once "../footer.php";
    exit;
}

if ($user->getRole() != 3) { //if user is not an admin
    if ($user->getRole() != 2 || $user->getId() != $dc->getInstructorId()) { //if user is not the instructor of this distribution choice
        echo '<meta http-equiv="refresh" content="0;url=../index.php">';
        exit;
    }
}
?>

<main>
    <div class="container-fluid d-flex flex-column align-items-center pb-5 pt-5" style="min-height: 90vh;">
        <div class="bg-white bg-opacity-50 p-5 rounded-5 shadow" style="width: 70%;">
            <h2 class="mb-3">Edit Distribution Choice(<?= $dc->getName(); ?>) for <?= $dist->getIdent() ?></h2>
            <hr>
            <form method="post" class="w-100">
                <input type="hidden" name="action" value="editChoice">
                <input type="hidden" name="id" value="<?= $dc->getId(); ?>">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= $dc->getName(); ?>"
                        required>
                </div>
                <div class="mb-3">
                    <label for="name" class="form-label">Instructor</label>
                    <input type="text" class="form-control" value="<?= $instructor->getNames(); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" class="form-control" required><?= $dc->getDescription(); ?>
                    </textarea>
                </div>
                <div class="mb-3">
                    <label for="min" class="form-label">Min</label>
                    <input type="number" id="min" name="min" class="form-control" value="<?= $dc->getMin(); ?>"
                        required>
                </div>
                <div class="mb-3">
                    <label for="max" class="form-label">Max</label>
                    <input type="number" id="max" name="max" class="form-control" value="<?= $dc->getMax(); ?>"
                        required>
                </div>
                <div class="form-check mb-3">
                    <input type="hidden" name="min_max_editble" value="0">
                    <input type="checkbox" id="min_max_editble" name="min_max_editble" class="form-check-input"
                        value="1" <?= $dc->getMinMaxEditable() ? 'checked' : '' ?>>
                    <label for="min_max_editble" class="form-check-label">
                        Min/Max editable by instructors
                    </label>
                </div>
                <button type="submit" class="btn btn-primary">Edit Distribution</button>
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
            e.preventDefault();
            $('.text-danger').remove();
            $.ajax({
                url: '../backend/ajax.php',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (res) {
                    if (res[0] == 1) {
                        <?php if ($user->getRole() == 3) {//if user is admin ?>
                            window.location = 'distributionView.php?id=<?= $dist->getId(); ?>';
                        <?php } else { //if user is teacher ?>
                            //TODO
                            window.location = '../index.php';
                        <?php } ?>
                    } else if (res[1]) {
                        $('[name="' + res[1] + '"]').after('<div class="text-danger">' + res[2] + '</div>');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error("AJAX error:", textStatus, errorThrown);
                    console.error("Raw response:", jqXHR.responseText);
                }
            });
        });
    });
</script>