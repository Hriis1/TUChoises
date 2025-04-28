<?php
require_once "../header.php";
?>

<main>
    <div class="container py-5">
        <h1 class="mb-4">Admin Panel</h1>
        <div class="row g-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title">Users</h5>
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary">Add User</button>
                            <button class="btn btn-primary">Import Users</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title">Distributions</h5>
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary">Add Distribution</button>
                            <button class="btn btn-primary">Import Distributions</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title">Majors</h5>
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary">Add Major</button>
                            <button class="btn btn-primary">Import Majors</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title">Faculties</h5>
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary">Add Faculty</button>
                            <button class="btn btn-primary">Import Faculties</button>
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