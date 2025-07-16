<?php
require_once "../header.php";
if ($user->getRole() != 3) {
    header("Location: ../index.php");
    exit;
}

$faculties = $mysqli->query("SELECT * FROM faculties WHERE deleted = 0 ORDER BY name");
?>
<main>
    <div class="container-fluid d-flex flex-column align-items-center pb-5 pt-5" style="min-height: 90vh;">
        <div class="bg-white bg-opacity-50 p-5 rounded-5 shadow" style="width: 70%;">
            <h2 class="mb-3">Add Distribution</h2>
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
                    <input type="number" class="form-control" id="semester_applicable" name="semester_applicable"
                        required min="1" max="10">
                </div>
                <div class="mb-3">
                    <label for="major" class="form-label">Major</label>
                    <select class="form-select" id="major" name="major" required>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="faculty" class="form-label">Faculty</label>
                    <select class="form-select" id="faculty" name="faculty" required>
                        <?php while ($f = $faculties->fetch_assoc()): ?>
                            <option value="<?= $f['short'] ?>"><?= htmlspecialchars($f['name']) ?></option>
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

        //Toggle major on change of type
        function toggleMajor() {
            if ($('#type').val() == 2) {
                $('#major').closest('.mb-3').hide().find('select').prop('required', false);
                $('#major').val(0);
            } else {
                $('#major').closest('.mb-3').show().find('select').prop('required', true);
                $("#faculty").trigger('change');
            }
        }
        toggleMajor();
        $('#type').on('change', toggleMajor);

        //Get majors when faculty changes
        $("#faculty").on('change', function () {
            //Only get them if type is избираема дисциплина
            if ($('#type').val() == 1) {
                const facultyShort = $(this).val();
                $.ajax({
                    url: '../backend/ajax.php',
                    method: 'POST',
                    data: {
                        action: "getMajorsOfFaculty",
                        facultyShort: facultyShort
                    },
                    dataType: 'json',
                    success: function (res) {
                        const $maj = $('#major').empty();
                        $.each(res, function (i, m) {
                            $maj.append($('<option>', { value: m.short, text: m.name }));
                        });
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.error("AJAX error:", textStatus, errorThrown);
                        console.error("Raw response:", jqXHR.responseText);
                    }
                });
            }
        }).trigger('change');


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
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error("AJAX error:", textStatus, errorThrown);
                    console.error("Raw response:", jqXHR.responseText);
                }
            });
        });
    });
</script>