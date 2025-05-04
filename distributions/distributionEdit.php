<?php
require_once "../header.php";

if ($user->getRole() != 3) {
    echo '<meta http-equiv="refresh" content="0;url=../index.php">';
    exit;
}

// if there is no get id
if (!isset($_GET["id"])) {
    echo '<meta http-equiv="refresh" content="0;url=../admin/adminPanel.php">';
}

//Try getting the dist
$dist = null;
try {
    $dist = new Distribution($_GET["id"], $mysqli);
} catch (Exception $e) {
    echo $e->getMessage();
    require_once "../footer.php";
    exit;
}

$majors = getNonDeletedFromDB("majors", $mysqli);
?>

<main>
    <div class="container-fluid d-flex flex-column align-items-center pb-5 pt-5" style="min-height: 90vh;">
        <div class="bg-white bg-opacity-50 p-5 rounded-5 shadow" style="width: 70%;">
            <h2 class="mb-3">Edit Distribution</h2>
            <hr>
            <form method="post" class="w-100">
                <input type="hidden" name="action" value="editDistribution">
                <input type="hidden" name="id" value="<?= $dist->getId(); ?>">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= $dist->getName(); ?>"
                        required>
                </div>
                <div class="mb-3">
                    <label for="ident" class="form-label">Ident</label>
                    <input type="text" class="form-control" id="ident" name="ident" value="<?= $dist->getIdent(); ?>"
                        required>
                </div>
                <div class="mb-3">
                    <label for="semester_applicable" class="form-label">Semester Applicable</label>
                    <input type="number" class="form-control" id="semester_applicable" name="semester_applicable"
                        value="<?= $dist->getSemesterApplicable(); ?>" required min="1" max="10">
                </div>
                <div class="mb-3">
                    <label for="major" class="form-label">Major</label>
                    <select class="form-select" id="major" name="major" required>
                        <?php foreach ($majors as $m): ?>
                            <option value="<?= $m['id'] ?>" <?= $m['id'] == $dist->getMajorId() ? 'selected' : '' ?>>
                                <?= htmlspecialchars($m['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="type" class="form-label">Type</label>
                    <select class="form-select" id="type" name="type" required>
                        <option value="1" <?= $dist->getType() == 1 ? 'selected' : '' ?>>Избираема дисциплина</option>
                        <option value="2" <?= $dist->getType() == 2 ? 'selected' : '' ?>>Дипломен ръководител</option>
                    </select>
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
                        window.location = 'distributionList.php';
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