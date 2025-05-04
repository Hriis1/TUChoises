<?php
require_once "../header.php";
if ($user->getRole() != 3) {
    echo '<meta http-equiv="refresh" content="0;url=../index.php">';
    exit;
}
if (!isset($_GET['dist_id']) || !ctype_digit($_GET['dist_id'])) {
    echo '<meta http-equiv="refresh" content="0;url=distributionList.php">';
    exit;
}
$dist_id = (int) $_GET['dist_id'];
$distribution = null;

//Try getting the dist
try {
    $distribution = new Distribution($dist_id, $mysqli);
} catch (Exception $e) {
    echo $e->getMessage();
    require_once "../footer.php";
    exit;
}

?>
<main>
    <div class="container-fluid d-flex flex-column align-items-center py-5" style="min-height:90vh">
        <div class="bg-white bg-opacity-50 p-5 rounded-5 shadow" style="width:70%">
            <h3 class="mb-3">Add Distribution Choices to <?= $distribution->getIdent(); ?></h3>
            <hr>
            <form id="choicesForm" method="post" class="w-100">
                <input type="hidden" name="action" value="addChoices">
                <input type="hidden" name="distribution" value="<?= $dist_id ?>">
                <div class="mb-3">
                    <label for="count" class="form-label">Number of Choices(max 10)</label>
                    <input type="number" id="count" name="count" class="form-control" min="1" max="10" required>
                </div>
                <div id="choicesContainer"></div>
                <button type="submit" class="btn btn-primary mt-3">Save Choices</button>
            </form>
        </div>
    </div>
</main>

<?php require_once "../footer.php" ?>

<script>
    $(function () {
        $('#count').on('input', function () {
            const container = $('#choicesContainer').empty();
            const val = parseInt(this.value, 10);
            if (!this.value || val < 1) return; // clear and stop if empty or <1
            const n = Math.min(10, val);
            for (let i = 1; i <= n; i++) {
                container.append(`
        <div class="border p-3 mb-3">
          <h5>Choice ${i}</h5>
          <div class="mb-3">
            <label class="form-label" for="name_${i}">Name</label>
            <input type="text" id="name_${i}" name="name[]" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label" for="instructor_${i}">Instructor</label>
            <input type="text" id="instructor_${i}" name="instructor[]" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label" for="description_${i}">Description</label>
            <textarea id="description_${i}" name="description[]" class="form-control" required></textarea>
          </div>
        </div>
      `);
            }
        });
    });
</script>