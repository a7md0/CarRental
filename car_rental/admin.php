<?php
require_once 'include/auto_loader.php';
require_once 'include/head.php';
require_once 'routes/routes.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <!-- <base href="/car_rental/"> -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $CURRENT_ROUTE->pageTitle ?> - Admin panel</title>

    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/index.css" rel="stylesheet">
    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }
    </style>

    <?php $CURRENT_ROUTE->includeHeader(); ?>
</head>

<body class="bg-light <?= join(' ', $CUSTOM_CLASSES['body']); ?>">
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark fixed-top bg-secondary">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Carousel</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link<?= $requestPage == 'home' ? ' active' : ''; ?>" href="?p=page">Home</a>
                        </li>
                        <?php if ($CURRENT_USER != null) { ?>
                            <li class="nav-item">
                                <a class="nav-link<?= $requestPage == 'cars' ? ' active' : ''; ?>" href="?p=cars">Cars</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle<?= in_array($requestPage, ['popular-reserved-cars', 'sales-revenue']) ? ' active' : '' ?>" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Reports</a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item<?= $requestPage == 'popular-reserved-cars' ? ' active' : ''; ?>" href="?p=popular-reserved-cars">Most popular reserved cars</a></li>
                                    <li><a class="dropdown-item<?= $requestPage == 'sales-revenue' ? ' active' : ''; ?>" href="?p=sales-revenue">Sales revenue</a></li>
                                </ul>
                            </li>
                        <?php } ?>
                    </ul>
                    <ul class="navbar-nav">
                        <?php if ($CURRENT_USER == null) { ?>
                            <li class="nav-item">
                                <a class="nav-link<?= $requestPage == 'sign-in' ? ' active' : ''; ?>" href="index.php?p=sign-in">Sign in</a>
                            </li>
                        <?php } else { ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false"><?= $CURRENT_USER->getFirstName() . ' ' . $CURRENT_USER->getLastName(); ?></a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="index.php">Back to portal</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" onclick="post(window.location.href, {logout: true});">Logout</a></li>
                                </ul>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="<?= join(' ', $CUSTOM_CLASSES['main']); ?>">
        <?php $CURRENT_ROUTE->includePage(); ?>


    </main>

    <?php if ($HIDE_FOOTER === false) { ?>
        <footer class="my-5 pt-5 text-muted text-center text-small">
            <p class="mb-1">&copy; <?= date("Y"); ?> Company</p>
        </footer>
    <?php } ?>

    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/index.js"></script>

    <?php $CURRENT_ROUTE->includeFooter(); ?>
</body>

</html>
