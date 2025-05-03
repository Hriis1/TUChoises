<?php
require_once "../header.php";

if ($user->getRole() != 3) {
    header("Location: ../index.php");
    exit;
}

$faculties = getNonDeletedFromDB("faculties", $mysqli);
?>

<main>
    <div class="container-fluid d-flex flex-column align-items-center pb-5 pt-5" style="min-height: 90vh;">
        <div class="bg-white bg-opacity-50 p-5 rounded-5 shadow" style="width: 70%;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">Faculty List</h2>
                <a href="facultyAdd.php" class="btn btn-primary px-4">Add Favulty</a>
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
                                <a href=""><i class="fa-solid fa-pen"></i></a>
                                <a href=""><i class="fa-solid fa-trash"></i></a>
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