<?php
require_once 'include/head.php';
require_once 'routes/routes.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <base href="/car_rental/">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $CURRENT_ROUTE->pageTitle ?> - CRS</title>

    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
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


    <!-- Custom styles for this template -->
    <link href="assets/bootstrap/css/carousel.css" rel="stylesheet">

    <?php $CURRENT_ROUTE->includeHeader(); ?>
</head>

<body class="<?= join(' ', $CUSTOM_CLASSES['body']); ?>">
    <header>
        <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Carousel</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <ul class="navbar-nav me-auto mb-2 mb-md-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="#">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Link</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
                        </li>
                    </ul>
                    <form class="d-flex">
                        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                        <button class="btn btn-outline-success" type="submit">Search</button>
                    </form>
                </div>
            </div>
        </nav>
    </header>

    <main class="<?= join(' ', $CUSTOM_CLASSES['main']); ?>">
        <?php $CURRENT_ROUTE->includePage(); ?>

        <?php if ($HIDE_FOOTER === false) { ?>
            <hr class="featurette-divider">

            <footer class="container">
                <p class="float-end"><a href="#">Back to top</a></p>
                <p>&copy; <?= date("Y"); ?> Company, Inc. &middot; <a href="#">Privacy</a> &middot; <a href="#">Terms</a></p>
            </footer>
        <?php } ?>
    </main>

    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <?php $CURRENT_ROUTE->includeFooter(); ?>
</body>

</html>
