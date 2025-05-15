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

//Try getting the major
$maj = null;
try {
    $maj = new Major($_GET["id"], $mysqli);
} catch (Exception $e) {
    echo $e->getMessage();
    require_once "../footer.php";
    exit;
}

$faculties = getNonDeletedFromDB("faculties", $mysqli);
?>

<main>
    <div class="container-fluid d-flex flex-column align-items-center pb-5 pt-5" style="min-height: 90vh;">
        <div class="bg-white bg-opacity-50 p-5 rounded-5 shadow" style="width: 70%;">
            <h2 class="mb-3">Edit Major</h2>
            <hr>
            <form method="post" class="w-100">
                <input type="hidden" name="action" value="editMajor">
                <input type="hidden" name="id" value="<?= $maj->getId(); ?>">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= $maj->getName(); ?>"
                        required>
                </div>
                <div class="mb-3">
                    <label for="short" class="form-label">Short</label>
                    <input type="text" class="form-control" id="short" name="short" value="<?= $maj->getShort(); ?>"
                        required>
                </div>
                <div class="mb-3">
                    <label for="faculty" class="form-label">Faculty</label>
                    <select class="form-select" id="faculty" name="faculty" required>
                        <?php foreach ($faculties as $f): ?>
                            <option value="<?= $f['short'] ?>" <?= $f['short'] == $maj->getFacultyShort() ? 'selected' : '' ?>>
                                <?= htmlspecialchars($f['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Edit Major</button>
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
                        window.location = 'majorList.php';
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