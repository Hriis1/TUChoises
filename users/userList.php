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
    setDeletedDB("users", $currID, $mysqli);

    // Send an alert
    $_SESSION["alert"] = [
        "type" => "danger",
        "text" => "User deleted successfully!"
    ];

    // Refresh page without GET params
    echo '<meta http-equiv="refresh" content="1;url=userList.php">';
}

$users = getNonDeletedFromDB("users", $mysqli);
?>

<main>
    <div class="container-fluid d-flex flex-column align-items-center pb-5 pt-5" style="min-height:90vh">
        <div class="bg-white bg-opacity-50 p-5 rounded-5 shadow" style="width:70%">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">User List</h2>
                <div>
                    <a href="userAdd.php" class="btn btn-primary px-4 me-2">Add User</a>
                    <button onclick="importUsers()" class="btn btn-success px-4">Import Users</button>
                </div>
            </div>
            <hr>
            <table id="table" class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Names</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>FN</th>
                        <th>Major</th>
                        <th>Faculty</th>
                        <th>Start Year</th>
                        <th>Active</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u) {
                        $majorName = "";
                        $facultyName = "";
                        //Try getting major
                        try {
                            $currMajor = new Major($u["major"], $mysqli);
                            $majorName = $currMajor->getName();
                        } catch (\Exception $th) {
                        }

                        //Try getting faculty
                        try {
                            $currFaculty = new Faculty($u["faculty"], $mysqli);
                            $facultyName = $currFaculty->getName();
                        } catch (\Exception $th) {
                        }
                        ?>
                        <tr>
                            <td><?= $u["id"] ?></td>
                            <td><?= $u["username"] ?></td>
                            <td><?= $u["names"] ?></td>
                            <td><?= $u["email"] ?></td>
                            <td>
                                <?php
                                switch ($u["role"]) {
                                    case '1':
                                        echo "Студент";
                                        break;
                                    case '2':
                                        echo "Преподавател";
                                        break;
                                    case '3':
                                        echo "Админ";
                                        break;
                                    default:
                                        break;
                                }
                                ?>
                            </td>
                            <td><?= $u["fn"] ?></td>
                            <td><?= $majorName; ?></td>
                            <td><?= $facultyName; ?></td>
                            <td><?= $u["start_year"] ?></td>
                            <td>
                                <?php if ($u["active"]) { ?>
                                    <span class="badge bg-success">Active</span>
                                <?php } else { ?>
                                    <span class="badge bg-danger">Inactive</span>
                                <?php } ?>
                            </td>
                            <td>
                                <?php if ($u["role"] != 3) { //Admins should not be able to change/delete other admin acc ?>
                                    <a href="userEdit.php?id=<?= $u["id"] ?>"><i class="fa-solid fa-pen"></i></a>
                                    <a href="userList.php?action=delete&id=<?= $u["id"] ?>"><i
                                            class="fa-solid fa-trash"></i></a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php require_once "../footer.php" ?>

<script>
    $(document).ready(function () {
        let table = new DataTable("#table", {
            columnDefs: [
                { targets: 10, width: "100px" }, //Actions
            ]
        });
    });
</script>