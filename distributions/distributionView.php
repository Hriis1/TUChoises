<?php
require_once "../header.php";

if ($user->getRole() != 3) {
    echo '<meta http-equiv="refresh" content="0;url=../index.php">';
    exit;
}
?>

<main></main>

<?php require_once "../footer.php"; ?>

<script>
    $(document).ready(function () {
    });
</script>