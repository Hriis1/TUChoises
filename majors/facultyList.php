<?php
require_once "../header.php";
if ($user->getRole() != 3) {
    header("Location: ../index.php");
    exit;
}

?>

<main>
    <div class="container-fluid d-flex flex-column align-items-center pb-5 pt-5" style="min-height: 90vh;">
        <div class="bg-white bg-opacity-50 p-5 rounded-5 shadow" style="width: 70%;">
            <h3 class="mb-3">Major List</h3>
            <hr>
            <form method="post" class="w-100">
                <input type="hidden" name="action" value="addMajor">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="short" class="form-label">Short</label>
                    <input type="text" class="form-control" id="short" name="short" required>
                </div>
                <div class="mb-3">
                    <label for="faculty_id" class="form-label">Faculty</label>
                    <select class="form-select" id="faculty_id" name="faculty_id" required>
                        <?php while ($f = $faculties->fetch_assoc()): ?>
                            <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Add Major</button>
            </form>
        </div>
    </div>
</main>