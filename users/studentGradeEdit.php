<?php
require_once "../header.php";

if ($user->getRole() != 3) {
    echo '<meta http-equiv="refresh" content="0;url=../index.php">';
    exit;
}

//if there is no get id
if (!isset($_GET["id"])) {
    echo '<meta http-equiv="refresh" content="0;url=../admin/adminPanel.php">';
}
//Try getting the grade
$grade = getFromDBByID("student_grades", $_GET["id"], $mysqli);
if (!$grade) { //if grade was not found
    echo "Grade not found";
    require_once "../footer.php";
    exit;
}
$userID = $grade["user_id"];
$user = getFromDBCondition("users", "WHERE id = $userID AND role = 1 AND deleted = 0", $mysqli);
if (!$user) { //if grade is not of a valid user
    echo "Invalid grade";
    require_once "../footer.php";
    exit;
}
?>


<main>
    <div class="container-fluid d-flex flex-column align-items-center pb-5 pt-5" style="min-height: 90vh;">
        <div class="bg-white bg-opacity-50 p-5 rounded-5 shadow" style="width: 70%;">
            <h2 class="mb-3">Edit Grade</h2>
            <hr>
            <form method="post" class="w-100">
                <input type="hidden" name="action" value="editStudentGrade">
                <input type="hidden" name="id" value="<?= $grade["id"]; ?>">
                <div class="mb-3">
                    <label for="user_names" class="form-label">Student</label>
                    <input type="text" class="form-control" id="user_names" name="user_names"
                        value="<?= $user[0]["names"]; ?>" disabled>
                </div>
                <div class="mb-3">
                    <label for="grade" class="form-label">Grade</label>
                    <input type="number" class="form-control" id="grade" name="grade" value="<?= $grade["grade"]; ?>"
                        step="0.01" required>
                </div>
                <div class="mb-3">
                    <label for="user_names" class="form-label">Semester</label>
                    <input type="text" class="form-control" id="semester" name="semester"
                        value="<?= $grade["semester"]; ?>" disabled>
                </div>
                <button type="submit" class="btn btn-primary">Edit Grade</button>
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
                        window.location = 'facultyList.php';
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