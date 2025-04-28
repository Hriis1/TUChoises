<?php
//Config stuff
require_once "backend/config/sessionConfig.php";
require_once "backend/config/dbConfig.php";
//require_once "backend/utils/dbUtils.php";

//Classes
require_once "backend/majors/Faculty.php";
require_once "backend/majors/Major.php";
require_once "backend/users/User.php";

//if user is not logged in
if (!isset($_SESSION["userID"])) {
    header("Location: login.php");
    exit;
}

//User is logged in
$user = new User($_SESSION["userID"], $mysqli);
echo $user->getNames();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Header</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">

    <link rel="stylesheet" href="css/navbar.css">

    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            padding-top: 3rem;
        }

        body {
            background-color: #E4E4E1;
            background-image: linear-gradient(to top, #bdc2e8 0%, #bdc2e8 1%, #e6dee9 100%);
            background-blend-mode: normal, multiply;
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment: fixed;
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
            <div class="container-fluid">
                <!-- logo on the left -->
                <a class="navbar-brand fw-bold" href="#">MyBrand</a>

                <!-- mobile toggler -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                    aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- links + CTA on the right -->
                <div class="collapse navbar-collapse" id="mainNav">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Features</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Pricing</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">About</a>
                        </li>
                        <li class="nav-item d-lg-block">
                            <button class="btn btn-pink ms-lg-3">Get Started</button>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

    </header>