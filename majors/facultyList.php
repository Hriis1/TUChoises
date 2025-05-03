<?php
require_once "../header.php";

if ($user->getRole() != 3) {
    echo '<meta http-equiv="refresh" content="0;url=../index.php">';
    exit;
}

//If action is delete
if (isset($_GET["action"]) && $_GET["action"] == "delete") {
    //Delete from db
    $currID = $_GET["id"];
    setDeletedDB("faculties", $currID, $mysqli);

    //Send an alert
    $_SESSION["alert"] = [
        "type" => "danger",
        "text" => "Faculty deleted successfully!"
    ];

    //Refresh page
    echo '<meta http-equiv="refresh" content="1;url=facultyList.php">';
}

$faculties = getNonDeletedFromDB("faculties", $mysqli);
?>

<main>
    <div class="container-fluid d-flex flex-column align-items-center pb-5 pt-5" style="min-height: 90vh;">
        <div class="bg-white bg-opacity-50 p-5 rounded-5 shadow" style="width: 70%;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">Faculty List</h2>
                <div>
                    <a href="facultyAdd.php" class="btn btn-primary px-4 me-2">Add Faculty</a>
                    <button onclick="importFaculties()" class="btn btn-success px-4">Import Faculties</button>
                </div>
            </div>

            <hr>
            <table id="table" class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Short</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($faculties as $curr) { ?>
                        <tr>
                            <td><?= $curr["id"]; ?></td>
                            <td><?= $curr["name"]; ?></td>
                            <td><?= $curr["short"]; ?></td>
                            <td>
                                <a href="facultyEdit.php?id=<?= $curr["id"]; ?>"><i class="fa-solid fa-pen"></i></a>
                                <a href="facultyList.php?action=delete&id=<?= $curr["id"]; ?>"><i
                                        class="fa-solid fa-trash"></i></a>
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
        let table = new DataTable("#table");
    });
</script>