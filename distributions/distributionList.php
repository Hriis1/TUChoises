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
    exit;
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
                    <button onclick="importData('../backend/utils/importData.php', 'importDistributions')"
                        class="btn btn-success px-4">Import Distributions</button>
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
                        <th>Major</th>
                        <th>Faculty</th>
                        <th>Type</th>
                        <th>Active</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($distributions as $d) {
                        $currDist = new Distribution($d["id"], $mysqli);
                        $majorName = "";
                        $facultyName = "";
                        //Try getting major
                        try {
                            $currMajor = new Major($currDist->getMajorID($mysqli), $mysqli);
                            $majorName = $currMajor->getName();
                        } catch (\Exception $th) {
                        }

                        //Try getting faculty
                        try {
                            $currFaculty = new Faculty($currDist->getFacultyID($mysqli), $mysqli);
                            $facultyName = $currFaculty->getName();
                        } catch (\Exception $th) {
                        }
                        ?>
                        <tr>
                            <td><?= $d["id"]; ?></td>
                            <td><?= $d["name"]; ?></td>
                            <td><?= $d["ident"]; ?></td>
                            <td><?= $d["semester_applicable"]; ?></td>
                            <td><?= $majorName; ?></td>
                            <td><?= $facultyName; ?></td>
                            <td>
                                <?php
                                if ($d["type"] == 1) {
                                    echo "Избираема дисциплина";
                                } else if ($d["type"] == 2) {
                                    echo "Дипломен ръководител";
                                } ?>
                            </td>
                            <td>
                                <?php if ($d["active"]) { ?>
                                    <span class="badge bg-success" style="cursor: pointer;" title="Deactivate distribution"
                                        onclick="switchDistribution(<?= $d['id'] ?>, 0)">Active</span>
                                <?php } else { ?>
                                    <span class="badge bg-danger" style="cursor: pointer;" title="Activate distribution"
                                        onclick="switchDistribution(<?= $d['id'] ?>, 1)">Inactive</span>
                                <?php } ?>
                            </td>
                            <td>
                                <a href="distributionView.php?id=<?= $d["id"] ?>">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="distributionChoiseAdd.php?dist_id=<?= $d["id"]; ?>">
                                    <i class="fa-solid fa-plus"></i>
                                </a>
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
    //Activate a distribution
    function switchDistribution(id, active) {

        //Prompt the user
        const confirmText = active != 0 ? "activate" : "deactivate";
        if (!confirm(`Are you sure you want to ${confirmText} this distribution?`))
            return;

        //Activate/deactivate
        $.ajax({
            type: 'POST',
            url: '../backend/ajax.php',
            data: {
                action: 'toggleDistribution',
                id: id,
                active: active
            },
            success: function (response) {

                if (active == 0) { //if distribution is being deactivated
                    //TODO: Calculate which students are distriuted where here
                }

                location.reload();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("AJAX error:", textStatus, errorThrown);
                console.error("Raw response:", jqXHR.responseText);
            }
        });
    }


    $(document).ready(function () {
        let table = new DataTable("#table", {
            columnDefs: [
                { targets: 8, width: "100px" }, //Actions
            ]
        });
    });

</script>