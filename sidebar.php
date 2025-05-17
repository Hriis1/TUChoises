<!-- Toggle button (visible only on small screens) -->
<nav class="navbar mt-5 d-md-none">
  <div class="container-fluid">
    <button class="btn btn-light btn-outline-secondary" type="button" data-bs-toggle="offcanvas"
      data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
      <span class="navbar-toggler-icon"></span> Dashboard
    </button>
  </div>
</nav>

<!-- Offcanvas sidebar for mobile -->
<div class="offcanvas offcanvas-start d-md-none" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="mobileSidebarLabel"><?= ucfirst($user->getRoleName()) . " dashboard"; ?></h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body p-0 d-flex flex-column">
    <ul class="nav nav-pills flex-column mb-auto">
      <li class="nav-item">
        <a href="<?= $projectRoot ?>/index.php" class="nav-link link-dark">Home</a>
      </li>
      <?php if ($user->getRole() == 1 || $user->getRole() == 2) { ?>
        <li class="nav-item">
          <a href="<?= $projectRoot ?>/distributions/myDistributions.php" class="nav-link link-dark">My distributions</a>
        </li>
      <?php } ?>
      <?php if ($user->getRole() == 3) { ?>
        <li class="nav-item">
          <a href="<?= $projectRoot ?>/admin/adminPanel.php" class="nav-link link-dark">Admin Panel</a>
        </li>
      <?php } ?>
    </ul>
    <hr>
  </div>
</div>

<!-- Permanent sidebar for md+ screens -->
<div id="sidebar-custom" class="d-none d-md-flex flex-column flex-shrink-0 p-3 bg-light position-fixed"
  style="top: 7vh; left: 0; width: 280px; height: 93vh; background-color: rgb(255 255 255 / 50%) !important;">
  <div class="d-flex align-items-center mb-3 me-md-auto link-dark text-decoration-none">
    <span class="fs-4 fw-bold"><?= ucfirst($user->getRoleName()) . " dashboard"; ?></span>
  </div>
  <hr>
  <ul class="nav nav-pills flex-column mb-auto">
    <li class="nav-item">
      <a href="<?= $projectRoot ?>/index.php" class="nav-link link-dark">Home</a>
    </li>
    <?php if ($user->getRole() == 1 || $user->getRole() == 2) { ?>
      <hr>
      <h5>Distributions</h5>
      <li class="nav-item">
        <a href="<?= $projectRoot ?>/distributions/myDistributions.php?condition=all" class="nav-link link-dark">All
          distributions</a>
      </li>
      <?php if ($user->getRole() == 1) { //student ?>
        <li class="nav-item">
          <a href="<?= $projectRoot ?>/distributions/myDistributions.php?condition=to_make"
            class="nav-link link-dark">Choice needed</a>
        </li>
        <li class="nav-item">
          <a href="<?= $projectRoot ?>/distributions/myDistributions.php?condition=chosen"
            class="nav-link link-dark">Choice made</a>
        </li>
      <?php } else { //teacher ?>
        <li class="nav-item">
          <a href="<?= $projectRoot ?>/distributions/myDistributions.php?condition=active" class="nav-link link-dark">Active
            distributions</a>
        </li>
        <li class="nav-item">
          <a href="<?= $projectRoot ?>/distributions/myDistributions.php?condition=inactive"
            class="nav-link link-dark">Inactive
            distributions</a>
        </li>
      <?php } ?>
    <?php } ?>
    <?php if ($user->getRole() == 3) { //admin ?>
      <li class="nav-item">
        <a href="<?= $projectRoot ?>/admin/adminPanel.php" class="nav-link link-dark">Admin Panel</a>
      </li>
    <?php } ?>
  </ul>
  <hr>
  <!--
  <div class="dropdown">
    <a href="#" class="d-flex align-items-center link-dark text-decoration-none dropdown-toggle" id="dropdownUser2"
       data-bs-toggle="dropdown" aria-expanded="false">
      <img src="https://github.com/mdo.png" alt="" width="32" height="32" class="rounded-circle me-2">
      <strong>mdo</strong>
    </a>
    <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser2">
      <li><a class="dropdown-item" href="#">Settings</a></li>
      <li><a class="dropdown-item" href="#">Profile</a></li>
      <li><hr class="dropdown-divider"></li>
      <li><a class="dropdown-item" href="#">Sign out</a></li>
    </ul>
  </div>
  -->
</div>

<style>
  /* hover effect */
  #sidebar-custom .nav-link:hover,
  .offcanvas-body .nav-link:hover {
    background-color: rgba(0, 0, 0, 0.1);
    color: #000;
    transition: background-color 0.3s ease, color 0.3s ease;
  }
</style>