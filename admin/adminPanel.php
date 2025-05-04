<?php
require_once "../header.php";

if ($user->getRole() != 3) {//if user is not admin 
    header("Location: ../index.php");
    exit;
}
?>

<link rel="stylesheet" href="..//admin/zaza.css">
<main>
    <div class="container-fluid d-flex flex-column align-items-center pb-5" style="height: 90vh;">
        <h1 class="text-center mb-4">Admin Panel</h1>
        <div class="bg-white bg-opacity-50 p-5 rounded-5 shadow w-100 h-100" style="max-width: 90%;">
            <!-- <h2 class="text-center mb-4">Admin Panel</h2> -->
            <div class="row g-4 h-100">
                <div class="col-sm-6 col-lg-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h5 class="card-title">Users</h5>
                            <div class="d-grid gap-2">
                                <a href="../users/userList.php" class="btn btn-outline-primary">View Users</a>
                                <a href="../users/userAdd.php" class="btn btn-outline-primary">Add User</a>
                                <button class="btn btn-success">Import Users</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h5 class="card-title">Distributions</h5>
                            <div class="d-grid gap-2">
                                <a href="../distributions/distributionList.php" class="btn btn-outline-primary">View
                                    Distributions</a>
                                <a href="../distributions/distributionAdd.php" class="btn btn-outline-primary">Add
                                    Distribution</a>
                                <button class="btn btn-success">Import Distributions</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h5 class="card-title">Majors</h5>
                            <div class="d-grid gap-2">
                                <a href="../majors/majorList.php" class="btn btn-outline-primary">View Majors</a>
                                <a href="../majors/majorAdd.php" class="btn btn-outline-primary">Add Major</a>
                                <button class="btn btn-success">Import Majors</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h5 class="card-title">Faculties</h5>
                            <div class="d-grid gap-2">
                                <a href="../majors/facultyList.php" class="btn btn-outline-primary">View
                                    Faculties</a>
                                <a href="../majors/facultyAdd.php" class="btn btn-outline-primary">Add Faculty</a>
                                <button class="btn btn-success" onclick="importFaculties()">Import Faculties</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
require_once "../footer.php";
?>