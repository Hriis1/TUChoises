<?php
require_once "../header.php";
if ($user->getRole() != 3) {
    header("Location: ../index.php");
    exit;
}
$majors = $mysqli->query("SELECT * FROM majors WHERE deleted = 0");
$faculties = $mysqli->query("SELECT * FROM faculties WHERE deleted = 0");
?>
<main>
    <div class="container-fluid d-flex flex-column align-items-center pb-5 pt-5" style="min-height:90vh">
        <div class="bg-white bg-opacity-50 p-5 rounded-5 shadow" style="width:70%">
            <h2 class="mb-3">Add User</h2>
            <hr>
            <form method="post" class="w-100">
                <input type="hidden" name="action" value="addUser">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="names" class="form-label">Names</label>
                    <input type="text" class="form-control" id="names" name="names" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3 teacher-field" style="display: none;">
                    <label for="pass" class="form-label">Password</label>
                    <input type="password" class="form-control" id="pass" name="pass">
                </div>
                <div class="mb-3 student-field">
                    <label for="fn" class="form-label">Faculty Number</label>
                    <input type="text" class="form-control" id="fn" name="fn" required>
                </div>
                <div class="mb-3 student-field">
                    <label for="start_year" class="form-label">Start Year</label>
                    <input type="number" class="form-control" id="start_year" name="start_year" required>
                </div>
                <div class="mb-3 student-field">
                    <label for="major" class="form-label">Major</label>
                    <select class="form-select" id="major" name="major" required>
                        <?php while ($m = $majors->fetch_assoc()): ?>
                            <option value="<?= $m['short'] ?>"><?= htmlspecialchars($m['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3 teacher-field" style="display: none;">
                    <label for="major" class="form-label">Faculty</label>
                    <select class="form-select" id="faculty" name="faculty" required>
                        <?php while ($f = $faculties->fetch_assoc()): ?>
                            <option value="<?= $f['short'] ?>"><?= htmlspecialchars($f['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="1">Student</option>
                        <option value="2">Teacher</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Add User</button>
            </form>
        </div>
    </div>
</main>

<?php require_once "../footer.php"; ?>

<script>
    $(function () {
        function toggleFields() {
            if ($('#role').val() == '2') {
                $('.teacher-field').show().find('input').attr('required', true);
                $('.student-field').hide().find('input, select').attr('required', false);
            } else {
                $('.student-field').show().find('input, select').attr('required', true);
                $('.teacher-field').hide().find('input').attr('required', false);
            }
        }
        $('#role').on('change', toggleFields);
        toggleFields();

        $('form').on('submit', function (e) {
            e.preventDefault();
            $('.text-danger').remove();
            $.ajax({
                url: '../backend/ajax.php',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (res) {
                    if (res[0] == 1) window.location = 'userList.php';
                    else if (res[1]) $('[name="' + res[1] + '"]').after('<div class="text-danger">' + res[2] + '</div>');
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error("AJAX error:", textStatus, errorThrown);
                    console.error("Raw response:", jqXHR.responseText);
                }
            });
        });
    });
</script>