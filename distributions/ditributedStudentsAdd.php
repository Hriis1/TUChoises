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
            <h3 class="mb-3">Distribute manually</h3>
            <hr>
            <form id="choicesForm" method="post" class="w-100">
                <input type="hidden" name="action" value="distributeManually">
                <div class="mb-3">
                    <label for="student" class="form-label">Student</label>
                    <select class="form-select" id="student" name="student" required>
                        <?php foreach ($students as $student): ?>
                            <option value="<?= $student["id"]; ?>"><?= $student["names"]; ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="distribution" class="form-label">Distribution</label>
                    <select class="form-select" id="distribution" name="distribution" required>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="choice" class="form-label">Choice</label>
                    <select class="form-select" id="choice" name="choice" required>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Distribute</button>
            </form>
        </div>
    </div>
</main>

<?php require_once "../footer.php"; ?>

<script>
    $(function () {

        //Get distributions
        $('#student').on('change', function () {
            const studentID = $(this).val();
            $('#distribution').empty();
            if (!studentID) return;
            $.ajax({
                url: '../backend/ajax.php',
                method: 'POST',
                data: {
                    action: 'getPossibleDistributions',
                    student: studentID
                },
                dataType: 'json',
                success: function (res) {
                    console.log("success");
                    if (res && Array.isArray(res)) {
                        res.forEach(function (item) {
                            $('#distribution').append('<option value="' + item.id + '">' + item.name + '</option>');
                        });
                    }

                    //Trigger change for distribution
                    $('#distribution').trigger('change');
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error("AJAX error:", textStatus, errorThrown);
                    console.error("Raw response:", jqXHR.responseText);
                }
            });
        });
        $('#student').trigger('change');

        //Get choices
        $('#distribution').on('change', function () {
            const distID = $(this).val();
            $('#choice').empty();
            if (!distID) return;
            $.ajax({
                url: '../backend/ajax.php',
                method: 'POST',
                data: {
                    action: 'getChoicesForDistribution',
                    distribution: distID
                },
                dataType: 'json',
                success: function (res) {
                    if (res && Array.isArray(res)) {
                        res.forEach(function (item) {
                            $('#choice').append('<option value="' + item.id + '">' + item.name + '</option>');
                        });
                    }
                }
            });
        });



        //Form submit
        $('form').on('submit', function (e) {
            e.preventDefault();
            $('.text-danger').remove();
            const studentID = $('select[name="student"]').val();

            $.ajax({
                url: '../backend/ajax.php',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (res) {
                    if (res[0] == 1) {
                        window.location = 'ditributedStudentsList.php?id=' + studentID;
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