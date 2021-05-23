<?php extract($VALUES); ?>

<style>
    div.content {
        margin: 3rem 2.5vw 0 5vw;
    }

    div#results {
        display: flex;
        align-items: center;
        justify-items: center;
        justify-content: start;
        align-content: center;
        flex-direction: row;
        flex-wrap: wrap;
    }

    div.filters {
        display: flex;
        flex-direction: column;
        flex-wrap: nowrap;
        align-content: space-evenly;
        justify-content: normal;
        align-items: stretch;
    }

    div.car-card {
        margin-left: 1em;
        margin-right: 1em;
        margin-bottom: 2em;

        background-color: white;
    }
</style>

<div class="container-fluid">
    <div class="content">
        <div class="row">
            <div class="col-md-3 filters">
                <div class="col-12" style="margin-bottom: 1em;">
                    <input type="search" class="form-control" placeholder="Search..." />
                </div>

                <div class="card">
                    <article class="card-group-item">
                        <header class="card-header">
                            <h6 class="title">Brands</h6>
                        </header>
                        <div class="filter-content">
                            <div class="card-body">
                                <select id="filter-brands" class="form-select" multiple>
                                    <?php
                                    foreach ($availableBrands as $brand) {
                                        echo '<option value="' . $brand . '" selected>' . $brand . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </article>

                    <article class="card-group-item">
                        <header class="card-header">
                            <h6 class="title">Types</h6>
                        </header>
                        <div class="filter-content">
                            <div class="card-body">
                                <select id="filter-types" class="form-select" multiple>
                                    <?php
                                    foreach ($availableTypes as $carType) {
                                        echo '<option value="' . $carType->getCarTypeId() . '" selected>' . $carType->getType() . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </article>

                    <article class="card-group-item">
                        <header class="card-header">
                            <h6 class="title">Price</h6>
                        </header>
                        <div class="filter-content">
                            <div class="card-body">
                                <div class="input-group mb-3">
                                    <span class="input-group-text">BD</span>
                                    <input type="number" class="form-control" value="<?= $minDailyRentRate; ?>" min="<?= $minDailyRentRate; ?>" max="<?= $maxDailyRentRate - 0.001; ?>" required>
                                    <span class="input-group-text">-</span>
                                    <input type="number" class="form-control" value="<?= $maxDailyRentRate; ?>" min="<?= $minDailyRentRate + 0.001; ?>" max="<?= $maxDailyRentRate; ?>" required>
                                </div>
                            </div>
                        </div>
                    </article>

                    <article class="card-group-item">
                        <header class="card-header">
                            <h6 class="title">Year</h6>
                        </header>
                        <div class="filter-content">
                            <div class="card-body">
                                <div class="input-group mb-3">
                                    <input type="number" class="form-control" value="<?= $minYear; ?>" min="<?= $minYear; ?>" max="<?= $maxYear - 1; ?>" required>
                                    <span class="input-group-text">-</span>
                                    <input type="number" class="form-control" value="<?= $maxYear; ?>" min="<?= $minYear + 1; ?>" max="<?= $maxYear; ?>" required>
                                </div>
                            </div>
                        </div>
                    </article>

                    <article class="card-group-item">
                        <header class="card-header">
                            <h6 class="title">Number of seats</h6>
                        </header>
                        <div class="filter-content">
                            <div class="card-body">
                                <div class="input-group mb-3">
                                    <input type="number" class="form-control" value="<?= $minSeats; ?>" min="<?= $minSeats; ?>" max="<?= $maxSeats - 1; ?>" required>
                                    <span class="input-group-text">-</span>
                                    <input type="number" class="form-control" value="<?= $maxSeats; ?>" min="<?= $minSeats + 1; ?>" max="<?= $maxSeats; ?>" required>
                                </div>
                            </div>
                        </div>
                    </article>
                </div> <!-- card.// -->
            </div>

            <div class="col-md-9">
                <div id="results">
                    <?php
                    // $carsLookup = new AdvanceCarsLookup('2021-05-11', '2021-05-12');
                    // $cars = $carsLookup->find();

                    // foreach ($cars as $car) {
                    ?>
                    <!-- <div class="card car-card" style="width: 18rem;">
                            <img src="<?= $car->getPreviewImage(); ?>" class="card-img-top" alt="...">
                            <div class="card-body text-center">
                                <h5 class="card-title"><?= $car->getCarModel()->getFullDisplayName(); ?></h5>
                                <p class="card-subtitle"><?= $car->getDailyRentRate(); ?> | <?= $car->getColor(); ?></p>
                                <a href="#" class="btn btn-primary">Rent</a>
                            </div>
                        </div> -->
                    <?php
                    //}
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
