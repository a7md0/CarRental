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
    <title><?= $CURRENT_ROUTE->pageTitle ?> - CRS</title>

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
        <nav class="navbar navbar-expand-lg navbar-dark fixed-top bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Carousel</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link<?= $requestPage == 'home' ? ' active' : ''; ?>" href="">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link<?= $requestPage == 'lookup-cars' ? ' active' : ''; ?>" href="?p=lookup-cars">Lookup cars</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav">
                        <?php if ($CURRENT_USER == null) { ?>
                        <li class="nav-item">
                            <a class="nav-link<?= $requestPage == 'sign-in' ? ' active' : ''; ?>" href="?p=sign-in">Sign in</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link<?= $requestPage == 'signup' ? ' active' : ''; ?>" href="?p=signup">Signup</a>
                        </li>
                        <?php } else { ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false"><?= $CURRENT_USER->getFirstName() . ' '. $CURRENT_USER->getLastName(); ?></a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="#">Action</a></li>
                                <li><a class="dropdown-item" href="#">Another action</a></li>
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
        <footer class="footer mt-auto py-3 bg-dark">
            <div class="container">
                <p class="float-end"><a href="#">Back to top</a></p>
                <span class="text-muted">
                    <p>&copy; <?= date("Y"); ?> Company, Inc. &middot; <a href="#">Privacy</a> &middot; <a href="#">Terms</a></p>
                </span>
            </div>
        </footer>
    <?php } ?>

    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/index.js"></script>

    <?php $CURRENT_ROUTE->includeFooter(); ?>
</body>

</html>
