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

    img.car-img {
        max-width: 286px;
        max-height: 130px;
        object-fit: contain;
    }
</style>

<div class="container-fluid">
    <div class="content">
        <div class="py-1 text-center">
            <h2>Cars</h2>
        </div>

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
                                    <label for="" class="form-label">Price (per day)</label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">BD</span>
                                        <input type="number" class="form-control" name="filter_min_price" value="<?= $minDailyRentRate; ?>" min="<?= $minDailyRentRate; ?>" max="<?= $maxDailyRentRate; ?>" step="0.001" data-trigger-filter="true" required>
                                        <span class="input-group-text">-</span>
                                        <input type="number" class="form-control" name="filter_max_price" value="<?= $maxDailyRentRate; ?>" min="<?= $minDailyRentRate; ?>" max="<?= $maxDailyRentRate; ?>" step="0.001" data-trigger-filter="true" required>
                                    </div>

                                    <label for="filter-types" class="form-label">Types </label> <a href="javascript:void(0)" class="fw-light fs-6 float-end select-all-btn" data-link-for="filter-types">(Select all)</a>
                                    <div class="input-group mb-3">
                                        <select id="filter-types" name="filter_types" class="form-select" data-trigger-filter="true" multiple required>
                                            <?php
                                            foreach ($availableTypes as $carType) {
                                                echo '<option value="' . $carType->getCarTypeId() . '" selected>' . $carType->getType() . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <label for="filter-brands" class="form-label">Brands</label> <a href="javascript:void(0)" class="fw-light fs-6 float-end select-all-btn" data-link-for="filter-brands">(Select all)</a>
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

                                    <label for="filter-colors" class="form-label">Colors</label> <a href="javascript:void(0)" class="fw-light fs-6 float-end select-all-btn" data-link-for="filter-colors">(Select all)</a>
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
                    <ul id="pagination" class="pagination justify-content-end">
                    </ul>
                </nav>

                <div class="h-75 d-none" id="results-feedback">
                    <div class="h-100 d-flex">
                        <div class="d-flex w-100 justify-content-center align-self-center">

                            <div class="spinner-grow text-dark" role="status" id="loading-spinner">
                                <span class="visually-hidden">Loading...</span>
                            </div>


                            <h2 class="lead" id="result-message"></h2>
                        </div>
                    </div>
                </div>

                <div id="results"></div>
            </div>
        </div>
    </div>
</div>
