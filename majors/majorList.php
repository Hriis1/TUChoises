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
    setDeletedDB("majors", $currID, $mysqli);

    // Send an alert
    $_SESSION["alert"] = [
        "type" => "danger",
        "text" => "Major deleted successfully!"
    ];

    // Refresh page without GET params
    echo '<meta http-equiv="refresh" content="1;url=majorList.php">';
}

$majors = getNonDeletedFromDB("majors", $mysqli);
?>

<main>
    <div class="container-fluid d-flex flex-column align-items-center pb-5 pt-5" style="min-height: 90vh;">
        <div class="bg-white bg-opacity-50 p-5 rounded-5 shadow" style="width: 70%;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">Major List</h2>
                <div>
                    <a href="majorAdd.php" class="btn btn-primary px-4 me-2">Add Major</a>
                    <button onclick="importMajors()" class="btn btn-success px-4">Import Majors</button>
                </div>
            </div>

            <hr>
            <table id="table" class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Short</th>
                        <th>Faculty</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($majors as $curr) {
                        $currFaculty = new Faculty($curr["faculty"], $mysqli);
                        ?>
                        <tr>
                            <td><?= $curr["id"]; ?></td>
                            <td><?= $curr["name"]; ?></td>
                            <td><?= $curr["short"]; ?></td>
                            <td><?= $currFaculty->getName(); ?></td>
                            <td>
                                <a href="majorEdit.php?id=<?= $curr["id"]; ?>"><i class="fa-solid fa-pen"></i></a>
                                <a href="majorList.php?action=delete&id=<?= $curr["id"]; ?>"><i
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