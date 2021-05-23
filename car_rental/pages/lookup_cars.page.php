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
                                    <option value="volvo">Volvo</option>
                                    <option value="saab">Saab</option>
                                    <option value="opel">Opel</option>
                                    <option value="audi">Audi</option>
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
                                    <option value="volvo">Volvo</option>
                                    <option value="saab">Saab</option>
                                    <option value="opel">Opel</option>
                                    <option value="audi">Audi</option>
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
                                    <input type="number" class="form-control" value="10" min="1" required>
                                    <span class="input-group-text">-</span>
                                    <input type="number" class="form-control" value="20" min="2"required>
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
                                    <input type="number" class="form-control" value="2016" min="2000" max="2100" required>
                                    <span class="input-group-text">-</span>
                                    <input type="number" class="form-control" value="2020" min="2000" max="2100" required>
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
                                    <input type="number" class="form-control" value="2" min="2" max="9" required>
                                    <span class="input-group-text">-</span>
                                    <input type="number" class="form-control" value="9" min="2" max="9" required>
                                </div>
                            </div>
                        </div>
                    </article>
                </div> <!-- card.// -->
            </div>

            <div class="col-md-9">
                <div id="results">
                    <?php
                    $carsLookup = new AdvanceCarsLookup('2021-05-11', '2021-05-12');
                    $cars = $carsLookup->find();

                    foreach ($cars as $car) {
                    ?>
                        <div class="card car-card" style="width: 18rem;">
                            <img src="<?= $car->getPreviewImage(); ?>" class="card-img-top" alt="...">
                            <div class="card-body text-center">
                                <h5 class="card-title"><?= $car->getCarModel()->getFullDisplayName(); ?></h5>
                                <p class="card-subtitle"><?= $car->getDailyRentRate(); ?> | <?= $car->getColor(); ?></p>
                                <a href="#" class="btn btn-primary">Rent</a>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
