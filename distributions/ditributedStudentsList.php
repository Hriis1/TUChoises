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
    setDeletedDB("distributed_students", $currID, $mysqli);

    // Send an alert
    $_SESSION["alert"] = [
        "type" => "danger",
        "text" => "Student distribution deleted successfully!"
    ];

    // Refresh page without GET params
    echo '<meta http-equiv="refresh" content="1;url=ditributedStudentsList.php">';
}

//Build condition and take from distributed_students
$title = "Distributed Students";
$condition = "WHERE deleted = 0";
if (isset($_GET["student_id"])) {
    $studentID = $_GET["student_id"];
    $student = getFromDBCondition("users", "WHERE id = $studentID AND role = 1 AND deleted = 0", $mysqli);
    if ($student) {
        $condition .= " AND student_id = $studentID";
        $title .= " : " . $student[0]["names"];
    }
}
$distributed_students = getFromDBCondition("distributed_students", $condition, $mysqli);

//Buffers
$studentsBuffer = [];
$distributionsBuffer = [];
$choicesBuffer = [];
$teachersBuffer = [];
?>

<main>
    <div class="container-fluid d-flex flex-column align-items-center pb-5 pt-5" style="min-height: 90vh;">
        <div class="bg-white bg-opacity-50 p-5 rounded-5 shadow" style="width: 70%;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0"><?= $title; ?></h2>
                <div>
                    <a href="ditributedStudentsAdd.php" class="btn btn-primary px-4 me-2">Distribute manualy</a>
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
                        <th>Distribution</th>
                        <th>Choice</th>
                        <th>Instructor</th>
                        <th>Distribution Semester</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($distributed_students as $curr) {
                        $teacher_id = 0;
                        $grade = "";
                        try {
                            if (!isset($studentsBuffer[$curr["student_id"]])) {
                                $studentsBuffer[$curr["student_id"]] = new User($curr["student_id"], $mysqli);
                            }
                            $student = $studentsBuffer[$curr["student_id"]];

                            if (!isset($distributionsBuffer[$curr["dist_id"]])) {
                                $distributionsBuffer[$curr["dist_id"]] = new Distribution($curr["dist_id"], $mysqli);
                            }
                            $distribution = $distributionsBuffer[$curr["dist_id"]];

                            //Get grade
                            if ($student && $distribution) {
                                $fn = $student->getFn();
                                $semester_grade = $distribution->getSemesterApplicable() - 1;
                                $grade = getFromDBCondition("student_grades", "WHERE student_fn = $fn AND semester = $semester_grade AND deleted = 0", $mysqli);
                                if ($grade) {
                                    $grade = $grade[0]["grade"];
                                }
                            }

                            if (!isset($choicesBuffer[$curr["dist_choice_id"]])) {
                                $choicesBuffer[$curr["dist_choice_id"]] = new DistributionChoice($curr["dist_choice_id"], $mysqli);
                            }
                            $choice = $choicesBuffer[$curr["dist_choice_id"]];

                            $teacher_id = $choice->getInstructorId();
                            if (!isset($teachersBuffer[$teacher_id])) {
                                $teachersBuffer[$teacher_id] = new User($teacher_id, $mysqli);
                            }
                            $teacher = $teachersBuffer[$teacher_id];
                        } catch (\Exception $e) {
                            //echo $e->getMessage();
                        }

                        $out_id = $curr["id"] ?? "";
                        $out_student_name = isset($studentsBuffer[$curr["student_id"]]) ? $studentsBuffer[$curr["student_id"]]->getNames() : "";
                        $out_fn = isset($studentsBuffer[$curr["student_id"]]) ? $studentsBuffer[$curr["student_id"]]->getFn() : "";
                        $out_dist_name = isset($distributionsBuffer[$curr["dist_id"]]) ? $distributionsBuffer[$curr["dist_id"]]->getName() : "";
                        $out_choice_name = isset($choicesBuffer[$curr["dist_choice_id"]]) ? $choicesBuffer[$curr["dist_choice_id"]]->getName() : "";
                        $out_teacher_name = isset($teachersBuffer[$teacher_id]) ? $teachersBuffer[$teacher_id]->getNames() : "";
                        $out_semester = isset($distributionsBuffer[$curr["dist_id"]]) ? $distributionsBuffer[$curr["dist_id"]]->getSemesterApplicable() : "";

                        ?>
                        <tr>
                            <td><?= $out_id; ?></td>
                            <td><?= $out_student_name; ?></td>
                            <td><?= $out_fn; ?></td>
                            <td><?= $grade ?></td>
                            <td><?= $out_dist_name; ?></td>
                            <td><?= $out_choice_name; ?></td>
                            <td><?= $out_teacher_name; ?></td>
                            <td><?= $out_semester; ?></td>
                            <td>
                                <a href="ditributedStudentEdit.php?id=<?= $curr["id"]; ?>"><i
                                        class="fa-solid fa-pen"></i></a>
                                <a href="ditributedStudentsList.php?action=delete&id=<?= $curr["id"]; ?>">
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
                [4, 'asc'],
                [5, 'asc']
            ],
            columnDefs: [
                { targets: 8, width: "100px" }, //Actions
            ]
        });
    });

</script>