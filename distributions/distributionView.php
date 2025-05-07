<?php
require_once "../header.php";

if ($user->getRole() != 3) {
    echo '<meta http-equiv="refresh" content="0;url=../index.php">';
    exit;
}

// If action is delete
if (isset($_GET["action"]) && $_GET["action"] == "delete") {
    // Delete from db
    $distID = $_GET["dist_id"];
    $currChoiceId = $_GET["choice_id"];
    setDeletedDB("distribution_choices", $currChoiceId, $mysqli);

    // Send an alert
    $_SESSION["alert"] = [
        "type" => "danger",
        "text" => "Distribution choice deleted successfully!"
    ];

    // Refresh page without GET params
    echo '<meta http-equiv="refresh" content="1;url=distributionView.php?id=' . $distID . '">';
    exit;
}

//Try getting the dist
$dist = null;
try {
    $dist = new Distribution($_GET["id"], $mysqli);
} catch (Exception $e) {
    echo $e->getMessage();
    require_once "../footer.php";
    exit;
}
$distID = $dist->getId();

$distChoices = getFromDBCondition("distribution_choices", "WHERE distribution = $distID AND deleted = 0", $mysqli);
?>

<main>
    <div class="container-fluid d-flex flex-column align-items-center pb-5 pt-5" style="min-height:90vh">
        <div class="bg-white bg-opacity-50 p-5 rounded-5 shadow" style="width:70%">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">Distribution Choices for <?= $dist->getIdent(); ?></h2>
                <div>
                    <a href="distributionChoiseAdd.php?dist_id=<?= $distID; ?>" class="btn btn-primary px-4 me-2">
                        Add Distribution Choice
                    </a>
                </div>
            </div>

            <hr>
            <table id="table" class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Instructor</th>
                        <th>Distribution</th>
                        <th>Description</th>
                        <th>Major</th>
                        <th>Faculty</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($distChoices as $dc) {
                        $majorName = "";
                        $facultyName = "";
                        //Try getting major
                        try {
                            $currMajor = new Major($dist->getMajorId(), $mysqli);
                            $majorName = $currMajor->getName();
                        } catch (\Exception $th) {
                        }

                        //Try getting faculty
                        try {
                            $currFaculty = new Faculty($dist->getFacultyId(), $mysqli);
                            $facultyName = $currFaculty->getName();
                        } catch (\Exception $th) {
                        }

                        $currTeacher = new User($dc["instructor"], $mysqli);
                        ?>
                        <tr>
                            <td><?= $dc["id"]; ?></td>
                            <td><?= $dc["name"]; ?></td>
                            <td><?= $currTeacher->getNames(); ?></td>
                            <td><?= $dist->getName(); ?></td>
                            <td><?= $dc["description"]; ?></td>
                            <td><?= $majorName; ?></td>
                            <td><?= $facultyName; ?></td>
                            <td>
                                <a href="distributionChoiceEdit.php?id=<?= $dc["id"]; ?>">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <a
                                    href="distributionView.php?action=delete&dist_id=<?= $dist->getId(); ?>&choice_id=<?= $dc["id"]; ?>">
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

<?php require_once "../footer.php"; ?>

<script>
    $(document).ready(function () {
        let table = new DataTable("#table", {
            columnDefs: [
                { targets: 7, width: "100px" }, //Actions
            ]
        });
    });

</script>