<?php
require_once "../header.php";
if ($user->getRole() != 3) {
    echo '<meta http-equiv="refresh" content="0;url=../index.php">';
    exit;
}
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$dist_row = getFromDBByID('distributed_students', $id, $mysqli);
if (!$dist_row) {
    echo '<meta http-equiv="refresh" content="0;url=ditributedStudentsList.php">';
    exit;
}
$student = getFromDBByID('users', $dist_row['student_id'], $mysqli);
$distribution = getFromDBByID('distributions', $dist_row['dist_id'], $mysqli);
$choices = getFromDBCondition('distribution_choices', 'WHERE deleted = 0 AND distribution = ' . $dist_row['dist_id'], $mysqli);
?>
<main>
    <div class="container-fluid d-flex flex-column align-items-center py-5" style="min-height:90vh">
        <div class="bg-white bg-opacity-50 p-5 rounded-5 shadow" style="width:70%">
            <h3 class="mb-3">Edit Student Distribution</h3>
            <hr>
            <form id="editChoicesForm" method="post" class="w-100">
                <input type="hidden" name="action" value="editDistributedStudent">
                <input type="hidden" name="id" value="<?= $id ?>">
                <div class="mb-3">
                    <label class="form-label">Student</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($student['names']) ?>" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">Distribution</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($distribution['name']) ?>"
                        disabled>
                </div>
                <div class="mb-3">
                    <label for="choice" class="form-label">Choice</label>
                    <select class="form-select" id="choice" name="choice" required>
                        <?php foreach ($choices as $choice): ?>
                            <option value="<?= $choice['id'] ?>" <?= $dist_row['dist_choice_id'] == $choice['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($choice['name']) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Save</button>
            </form>
        </div>
    </div>
</main>
<?php require_once "../footer.php"; ?>
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
                    if (res[0] == 1 || res[0] == -1) {
                        window.location = 'ditributedStudentsList.php?id=<?= $student['id'] ?>';
                    } else if (res[1]) {
                        $('#' + res[1]).after('<div class="text-danger">' + res[2] + '</div>');
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