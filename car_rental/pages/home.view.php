<?php

/**
 * @var User $CURRENT_USER
 */
?>
<div id="myCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
    </div>
    <div class="carousel-inner">
        <div class="carousel-item active">
            <svg class="bd-placeholder-img" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" preserveAspectRatio="xMidYMid slice" focusable="false">
                <rect width="100%" height="100%" fill="#777" />
            </svg>

            <div class="container">
                <div class="carousel-caption text-start">
                    <?php if ($CURRENT_USER === null) { ?>
                        <h1>Create new account</h1>
                        <p>Create your account today and start reserving your car immediately.</p>
                        <p><a class="btn btn-lg btn-primary" href="?p=signup">Sign up today</a></p>
                    <?php } else { ?>
                        <h1>Lookup cars</h1>
                        <p>Start your reservation process now and pick your preferred car.</p>
                        <p><a class="btn btn-lg btn-primary" href="?p=lookup-cars">Lookup car</a></p>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="carousel-item">
            <svg class="bd-placeholder-img" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" preserveAspectRatio="xMidYMid slice" focusable="false">
                <rect width="100%" height="100%" fill="#777" />
            </svg>

            <div class="container">
                <div class="carousel-caption text-end">
                    <?php if ($CURRENT_USER === null) { ?>
                        <h1>Lookup cars</h1>
                        <p>Start your reservation process now and pick your preferred car.</p>
                        <p><a class="btn btn-lg btn-primary" href="?p=lookup-cars">Lookup car</a></p>
                    <?php } else { ?>
                        <h1>Lookup reservation</h1>
                        <p>Lookup your reservations and amend or cancel existing ones.</p>
                        <p><a class="btn btn-lg btn-primary" href="?p=view-reservation">Lookup car</a></p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#myCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#myCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>
