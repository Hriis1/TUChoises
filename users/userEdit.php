<?php
require_once "../header.php";

if ($user->getRole() != 3) {
    echo '<meta http-equiv="refresh" content="0;url=../index.php">';
    exit;
}

// if no id
if (!isset($_GET["id"])) {
    echo '<meta http-equiv="refresh" content="0;url=../admin/adminPanel.php">';
}

//Try getting the user
$userObj = null;
try {
    $userObj = new User($_GET["id"], $mysqli);
} catch (Exception $e) {
    echo $e->getMessage();
    require_once "../footer.php";
    exit;
}

$majors = getNonDeletedFromDB("majors", $mysqli);
$faculties = getNonDeletedFromDB("faculties", $mysqli);
?>

<main>
    <div class="container-fluid d-flex flex-column align-items-center pb-5 pt-5" style="min-height:90vh">
        <div class="bg-white bg-opacity-50 p-5 rounded-5 shadow" style="width:70%">
            <h2 class="mb-3">Edit User</h2>
            <hr>
            <form method="post" class="w-100">
                <input type="hidden" name="action" value="editUser">
                <input type="hidden" name="id" value="<?= $userObj->getId() ?>">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username"
                        value="<?= $userObj->getUsername() ?>" required>
                </div>
                <div class="mb-3">
                    <label for="names" class="form-label">Names</label>
                    <input type="text" class="form-control" id="names" name="names" value="<?= $userObj->getNames() ?>"
                        required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= $userObj->getEmail() ?>"
                        required>
                </div>
                <div class="mb-3 student-field">
                    <label for="fn" class="form-label student-field-label">Faculty Number</label>
                    <input type="text" class="form-control student-field" id="fn" name="fn"
                        value="<?= $userObj->getFn() ?>">
                </div>
                <div class="mb-3 student-field">
                    <label for="start_year" class="form-label">Start Year</label>
                    <input type="number" class="form-control" id="start_year" name="start_year"
                        value="<?= $userObj->getStartYear() ?>">
                </div>
                <div class="mb-3 student-field">
                    <label for="major" class="form-label">Major</label>
                    <select class="form-select" id="major" name="major" required>
                        <?php foreach ($majors as $m): ?>
                            <option value="<?= $m['short'] ?>" <?= $m['short'] == $userObj->getMajorShort() ? 'selected' : '' ?>>
                                <?= htmlspecialchars($m['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3 teacher-field" style="display: none;">
                    <label for="major" class="form-label">Faculty</label>
                    <select class="form-select" id="faculty" name="faculty" required>
                        <?php foreach ($faculties as $f): ?>
                            <option value="<?= $f['short'] ?>" <?= $f['short'] == $userObj->getFacultyShort() ? 'selected' : '' ?>>
                                <?= htmlspecialchars($f['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <input type="text" class="form-control" id="type" name="type"
                        value="<?= $userObj->getRole() == 1 ? 'Student' : 'Teacher' ?>" readonly>
                </div>
                <button type="submit" class="btn btn-primary">Edit User</button>
            </form>
        </div>
    </div>
</main>

<?php require_once "../footer.php" ?>

<script>
    $(function () {
        function toggleFields() {
            if (<?= $userObj->getRole(); ?> == '2') {
                $('.teacher-field').show().find('input').attr('required', true);
                $('.student-field').hide().find('input, select').attr('required', false);
            } else {
                $('.student-field').show().find('input, select').attr('required', true);
                $('.teacher-field').hide().find('input').attr('required', false);
            }
        }
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
                error: function (j, q, e) { console.error("AJAX error:", q, e); console.error("Raw:", j.responseText); }
            });
        });
    });
</script>