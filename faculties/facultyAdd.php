<?php
require_once "../header.php";

// if user is not admin
if ($user->getRole() != 3) {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'addFaculty') {
    $name = $mysqli->real_escape_string($_POST['name']);
    $short = $mysqli->real_escape_string($_POST['short']);
    $mysqli->query("INSERT INTO faculties (name, short) VALUES ('$name', '$short')");

    //Success
    if ($mysqli->affected_rows === 1) {
        echo json_encode([1, "", ""]);
        exit;
    }

    //Fail
    echo json_encode([0, "alert", "Error Adding Faculty"]);
}
?>

<main>
    <div class="container-fluid d-flex flex-column align-items-center pb-5 pt-5" style="min-height: 90vh;">
        <div class="bg-white bg-opacity-50 p-5 rounded-5 shadow" style="width: 70%;">
            <h3 class="mb-3">Add Faculty</h3>
            <hr>
            <form method="post" class="w-100">
                <input type="hidden" name="action" value="addFaculty">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name">
                </div>
                <div class="mb-3">
                    <label for="short" class="form-label">Short</label>
                    <input type="text" class="form-control" id="short" name="short">
                </div>
                <button type="submit" class="btn btn-primary">Add Faculty</button>
            </form>
        </div>
    </div>
</main>




<?php
require_once "../footer.php";
?>