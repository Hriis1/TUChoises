<?php
require_once "../header.php";

if ($user->getRole() != 3) {
    echo '<meta http-equiv="refresh" content="0;url=../index.php">';
    exit;
}

// If action is delete
if (isset($_GET["action"]) && $_GET["action"] == "delete") {
    // Delete from db
    $currID = $_GET["id"];
    setDeletedDB("distributions", $currID, $mysqli);

    // Send an alert
    $_SESSION["alert"] = [
        "type" => "danger",
        "text" => "Distribution deleted successfully!"
    ];

    // Refresh page without GET params
    echo '<meta http-equiv="refresh" content="1;url=distributionList.php">';
}

$distributions = getNonDeletedFromDB("distributions", $mysqli);
?>

<main>
    <div class="container-fluid d-flex flex-column align-items-center pb-5 pt-5" style="min-height:90vh">
        <div class="bg-white bg-opacity-50 p-5 rounded-5 shadow" style="width:70%">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">Distribution List</h2>
                <div>
                    <a href="distributionAdd.php" class="btn btn-primary px-4 me-2">Add Distribution</a>
                </div>
            </div>

            <hr>
            <table id="table" class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Ident</th>
                        <th>Semester</th>
                        <th>Major ID</th>
                        <th>Type</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($distributions as $d) {
                        $currMajor = new Major($d["major"], $mysqli);
                        ?>
                        <tr>
                            <td><?= $d["id"]; ?></td>
                            <td><?= $d["name"]; ?></td>
                            <td><?= $d["ident"]; ?></td>
                            <td><?= $d["semester_applicable"]; ?></td>
                            <td><?= $currMajor->getName(); ?></td>
                            <td>
                                <?php
                                if ($d["type"] == 1) {
                                    echo "Избираема дисциплина";
                                } else if ($d["type"] == 2) {
                                    echo "Дипломен ръководител";
                                } ?>
                            </td>
                            <td>
                                <a href="distributionEdit.php?id=<?= $d["id"]; ?>">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <a href="distributionList.php?action=delete&id=<?= $d["id"]; ?>">
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
        let table = new DataTable("#table");
    });
</script>