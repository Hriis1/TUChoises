<?php
require_once "../header.php";
if ($user->getRole() != 3) {
    echo '<meta http-equiv="refresh" content="0;url=../index.php">';
    exit;
}
if (!isset($_GET['dist_id']) || !ctype_digit($_GET['dist_id'])) {
    echo '<meta http-equiv="refresh" content="0;url=distributionList.php">';
    exit;
}
$dist_id = (int) $_GET['dist_id'];
$distribution = null;

//Try getting the dist
try {
    $distribution = new Distribution($dist_id, $mysqli);
} catch (Exception $e) {
    echo $e->getMessage();
    require_once "../footer.php";
    exit;
}

//Get teachers
$teachers = null;
$distType = $distribution->getType();
if ($distType == 1 || $distType == 2) { //if type is valid
    $distFac = $distribution->getFacultyShort();
    $teachers = getFromDBCondition("users", "WHERE role = 2 AND faculty = '$distFac' AND active = 1 AND deleted = 0", $mysqli);
} else {
    echo "Error: Invalid distribution type";
    require_once "../footer.php";
    exit;
}
?>
<main>
    <div class="container-fluid d-flex flex-column align-items-center py-5" style="min-height:90vh">
        <div class="bg-white bg-opacity-50 p-5 rounded-5 shadow" style="width:70%">
            <h3 class="mb-3">Add Distribution Choices to <?= $distribution->getIdent(); ?></h3>
            <hr>
            <form id="choicesForm" method="post" class="w-100">
                <input type="hidden" name="action" value="addChoices">
                <input type="hidden" name="distribution" value="<?= $dist_id ?>">
                <input type="hidden" name="distType" value="<?= $distType ?>">
                <div class="mb-3">
                    <label for="count" class="form-label">Number of Choices(max 10)</label>
                    <input type="number" id="count" name="count" class="form-control" min="1" max="10" required>
                </div>
                <div id="choicesContainer"></div>
                <button type="submit" class="btn btn-primary mt-3">Save Choices</button>
            </form>
        </div>
    </div>
</main>

<?php require_once "../footer.php" ?>

<script>
    $(function () {
        //Get the fields for the choices
        $('#count').on('input', function () {
            const container = $('#choicesContainer').empty();
            const val = parseInt(this.value, 10);
            if (!this.value || val < 1) return; // clear and stop if empty or <1
            const n = Math.min(10, val);
            for (let i = 1; i <= n; i++) {
                container.append(`
                <div class="border p-3 mb-3">
                    <h5>Choice ${i}</h5>
                    <div class="mb-3">
                        <label for="name_${i}" class="form-label">Name</label>
                        <input type="text" id="name_${i}" name="name[]" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="instructor_${i}" class="form-label">Instructor</label>
                        <select id="instructor_${i}" name="instructor[]" class="form-select" required>
                            <?php foreach ($teachers as $t): ?>
                            <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['names']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="description_${i}" class="form-label">Description</label>
                        <textarea id="description_${i}" name="description[]" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="min_${i}" class="form-label">Min</label>
                        <input type="number" id="min_${i}" name="min[]" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="max_${i}" class="form-label">Max</label>
                        <input type="number" id="max_${i}" name="max[]" class="form-control" required>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" id="min_max_editble_${i}" name="min_max_editble[]" class="form-check-input" checked>
                        <label for="min_max_editble_${i}" class="form-check-label">Min/Max editable by instructors</label>
                    </div>
                </div>
                `);
            }
        });

        //Submit the form 
        $('#choicesForm').on('submit', function (e) {
            e.preventDefault();
            $('.text-danger').remove();
            $.ajax({
                url: '../backend/ajax.php',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (res) {
                    if (res[0] == 1) {
                        window.location = 'distributionView.php?id=<?= $distribution->getId(); ?>';
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