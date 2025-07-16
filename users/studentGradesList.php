<?php
require_once "../header.php";

//If uer is not an admin
if ($user->getRole() != 3) {
    echo '<meta http-equiv="refresh" content="0;url=../index.php">';
    exit;
}

// If action is delete
if (isset($_GET["action"]) && $_GET["action"] == "delete") {
    // Delete from db
    $currID = $_GET["id"];
    deleteFromDB("student_grades", $currID, $mysqli);

    // Send an alert
    $_SESSION["alert"] = [
        "type" => "danger",
        "text" => "Grade deleted successfully!"
    ];

    // Refresh page without GET params
    echo '<meta http-equiv="refresh" content="1;url=studentGradesList.php">';
}

//Build condition and take grades
$condition = "WHERE deleted = 0";
if (isset($_GET["id"])) {
    $studentID = $_GET["id"];
    $student = getFromDBCondition("users", "WHERE id = $studentID AND role = 1 AND deleted = 0", $mysqli);
    if ($student) {
        $fn = trim($student[0]["fn"]);
        $condition .= " AND student_fn = $fn";
    }
}
$grades = getFromDBCondition("student_grades", $condition, $mysqli);
?>

<main>
    <div class="container-fluid d-flex flex-column align-items-center pb-5 pt-5" style="min-height: 90vh;">
        <div class="bg-white bg-opacity-50 p-5 rounded-5 shadow" style="width: 70%;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">Grades List
                    <?php if (isset($student)) {
                        echo "for " . $student[0]["names"];
                    } ?>
                </h2>
                <div>
                    <a href="studentGradesAdd.php" class="btn btn-primary px-4 me-2">Add Grade</a>
                    <button onclick="importData('../backend/utils/importData.php', 'importGrades')"
                        class="btn btn-success px-4">Import Grades</button>
                </div>
            </div>

            <hr>
            <table id="table" class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student</th>
                        <th>FN</th>
                        <th>Grade</th>
                        <th>Semester</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($grades as $curr) {
                        $fn = $curr["student_fn"];
                        $names = "";
                        $student = getFromDBCondition("users", "WHERE fn = $fn AND role = 1 AND deleted = 0", $mysqli);
                        if ($student) {
                            $names = $student[0]["names"];
                        }

                        ?>
                        <tr>
                            <td><?= $curr["id"]; ?></td>
                            <td><?= $names ?></td>
                            <td><?= $fn ?></td>
                            <td><?= $curr["grade"]; ?></td>
                            <td><?= $curr["semester"]; ?></td>
                            <td>
                                <a href="studentGradeEdit.php?id=<?= $curr["id"]; ?>"><i class="fa-solid fa-pen"></i></a>
                                <a href="studentGradesList.php?action=delete&id=<?= $curr["id"]; ?>">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php
require_once "../footer.php";
?>

<script>
    $(document).ready(function () {
        let table = new DataTable("#table", {
            order: [
                [2, 'asc'],
                [4, 'asc']
            ]
        });
    });

</script>