<?php
require_once "../header.php";
if ($user->getRole() != 3) {
    echo '<meta http-equiv="refresh" content="0;url=../index.php">';
    exit;
}

$students = getFromDBCondition("users", "WHERE role = 1 AND deleted = 0 ORDER BY names", $mysqli);

?>
<main>
    <div class="container-fluid d-flex flex-column align-items-center py-5" style="min-height:90vh">
        <div class="bg-white bg-opacity-50 p-5 rounded-5 shadow" style="width:70%">
            <h3 class="mb-3">Add Grades</h3>
            <hr>
            <form id="choicesForm" method="post" class="w-100">
                <input type="hidden" name="action" value="addStudentGrades">
                <div class="mb-3">
                    <label for="student" class="form-label">Student</label>
                    <select class="form-select" id="student" name="student" required>
                        <?php foreach ($students as $student): ?>
                            <option value="<?= $student["fn"]; ?>"><?= $student["names"]; ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="count" class="form-label">Number of Grades(max 10)</label>
                    <input type="number" id="count" name="count" class="form-control" min="1" max="10" required>
                </div>
                <div id="gradesContainer"></div>
                <button type="submit" class="btn btn-primary mt-3">Save Grades</button>
            </form>
        </div>
    </div>
</main>

<?php require_once "../footer.php"; ?>

<script>
    $(function () {
        //Get the fields for the grades
        $('#count').on('input', function () {
            const count = parseInt($(this).val());
            const container = $('#gradesContainer');
            container.empty();

            if (count > 0 && count <= 10) {
                for (let i = 0; i < count; i++) {
                    container.append(`
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">Grade ${i + 1}</label>
                                <input type="number" id="grade-${i}" name="grades[]" class="form-control" min="2" max="6" step="0.01" required>
                            </div>
                            <div class="col">
                                <label class="form-label">Semester ${i + 1}</label>
                                <input type="number" id="semester-${i}" name="semesters[]" class="form-control" min="1" max="10" required>
                            </div>
                        </div>
                    `);
                }
            }
        });

        //Form submit
        $('form').on('submit', function (e) {
            e.preventDefault();
            $('.text-danger').remove();
            const studentFN = $('select[name="student"]').val();

            $.ajax({
                url: '../backend/ajax.php',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (res) {
                    if (res[0] == 1) {
                        window.location = 'studentGradesList.php?fn=' + studentFN;
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