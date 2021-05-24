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
                <form name="filter_form">
                    <div style="margin-bottom: 1em;">
                        <input type="search" name="filter_search" class="form-control" placeholder="Search..." data-trigger-filter="true" />
                    </div>

                    <div class="card">
                        <article class="card-group-item">
                            <header class="card-header">
                                <h6 class="title">Filters</h6>
                            </header>
                            <div class="filter-content">
                                <div class="card-body">
                                    <label for="filter-pickup-date" class="form-label">Preferred reservation period<span id="reservation-period-in-days"></span></label>
                                    <div class="input-group mb-3">
                                        <input type="date" class="form-control" name="filter_pickup_date" id="filter-pickup-date" data-trigger-filter="true" required>
                                        <span class="input-group-text">-</span>
                                        <input type="date" class="form-control" name="filter_return_date" data-trigger-filter="true" required>
                                    </div>

                                    <label for="" class="form-label">Price (per day)</label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">BD</span>
                                        <input type="number" class="form-control" name="filter_min_price" value="<?= $minDailyRentRate; ?>" min="<?= $minDailyRentRate; ?>" max="<?= $maxDailyRentRate; ?>" step="0.001" data-trigger-filter="true" required>
                                        <span class="input-group-text">-</span>
                                        <input type="number" class="form-control" name="filter_max_price" value="<?= $maxDailyRentRate; ?>" min="<?= $minDailyRentRate; ?>" max="<?= $maxDailyRentRate; ?>" step="0.001" data-trigger-filter="true" required>
                                    </div>

                                    <label for="filter-types" class="form-label">Types</label>
                                    <div class="input-group mb-3">
                                        <select id="filter-types" name="filter_types" class="form-select" data-trigger-filter="true" multiple required>
                                            <?php
                                            foreach ($availableTypes as $carType) {
                                                echo '<option value="' . $carType->getCarTypeId() . '" selected>' . $carType->getType() . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <label for="filter-brands" class="form-label">Brands</label>
                                    <div class="input-group mb-3">
                                        <select id="filter-brands" name="filter_brands" class="form-select" data-trigger-filter="true" multiple required>
                                            <?php
                                            foreach ($availableBrands as $brand) {
                                                echo '<option value="' . $brand . '" selected>' . $brand . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <label for="" class="form-label">Model Year</label>
                                    <div class="input-group mb-3">
                                        <input type="number" class="form-control" name="filter_min_year" value="<?= $minYear; ?>" min="<?= $minYear; ?>" max="<?= $maxYear; ?>" step="1" data-trigger-filter="true" required>
                                        <span class="input-group-text">-</span>
                                        <input type="number" class="form-control" name="filter_max_year" value="<?= $maxYear; ?>" min="<?= $minYear; ?>" max="<?= $maxYear; ?>" step="1" data-trigger-filter="true" required>
                                    </div>

                                    <label for="filter-colors" class="form-label">Colors</label>
                                    <div class="input-group mb-3">
                                        <select id="filter-colors" name="filter_colors" class="form-select" data-trigger-filter="true" multiple required>
                                            <?php
                                            foreach ($availableColors as $color) {
                                                echo '<option value="' . $color . '" selected>' . $color . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <label for="" class="form-label">Number of seats</label>
                                    <div class="input-group mb-3">
                                        <input type="number" class="form-control" name="filter_min_seats" value="<?= $minSeats; ?>" min="<?= $minSeats; ?>" max="<?= $maxSeats; ?>" step="1" data-trigger-filter="true" required>
                                        <span class="input-group-text">-</span>
                                        <input type="number" class="form-control" name="filter_max_seats" value="<?= $maxSeats; ?>" min="<?= $minSeats; ?>" max="<?= $maxSeats; ?>" step="1" data-trigger-filter="true" required>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                </form>
            </div>

            <div class="col-md-9">
                <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-end">
                        <li class="page-item disabled"><a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a></li>
                        <li class="page-item"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item"><a class="page-link" href="#">Next</a></li>
                    </ul>
                </nav>

                <div id="results"></div>
            </div>
        </div>
    </div>
</div>
