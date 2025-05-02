<?php
require_once "../header.php";
if ($user->getRole() != 3) {
    header("Location: ../index.php");
    exit;
}
$majors = $mysqli->query("SELECT id, name FROM majors WHERE deleted = 0");
?>
<main>
    <div class="container-fluid d-flex flex-column align-items-center pb-5 pt-5" style="min-height: 90vh;">
        <div class="bg-white bg-opacity-50 p-5 rounded-5 shadow" style="width: 70%;">
            <h3 class="mb-3">Add Distribution</h3>
            <hr>
            <form method="post" class="w-100">
                <input type="hidden" name="action" value="addDistribution">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="ident" class="form-label">Ident</label>
                    <input type="text" class="form-control" id="ident" name="ident" required>
                </div>
                <div class="mb-3">
                    <label for="semester_applicable" class="form-label">Semester</label>
                    <input type="number" class="form-control" id="semester_applicable" name="semester_applicable" required
                        min="1" max="10">
                </div>
                <div class="mb-3">
                    <label for="major" class="form-label">Major</label>
                    <select class="form-select" id="major" name="major" required>
                        <?php while ($m = $majors->fetch_assoc()): ?>
                            <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="type" class="form-label">Type</label>
                    <select class="form-select" id="type" name="type" required>
                        <option value="1">Избираема дисциплина</option>
                        <option value="2">Дипломен ръководител</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Add Distribution</button>
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
                        $('[name="' + res[1] + '"]')
                            .after('<div class="text-danger">' + res[2] + '</div>');
                    }
                }
            });
        });
    });
</script>