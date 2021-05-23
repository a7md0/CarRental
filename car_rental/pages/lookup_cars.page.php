<style>
    div.content {
        margin: 3rem 10vw 0 10vw;
    }

    div#results {
        display: flex;
        align-items: center;
        justify-items: center;
        justify-content: center;
        align-content: center;
        flex-direction: row;
        flex-wrap: wrap;
    }

    div.card {
        margin-right: 3em;
        margin-bottom: 2em;
    }
</style>

<div class="content">

    <div class="input-group">
        <div class="form-outline">
            <input type="search" id="form1" class="form-control" />
            <label class="form-label" for="form1">Search</label>
        </div>
        <button type="button" class="btn btn-primary">
            <i class="fas fa-search"></i>
        </button>
    </div>

    <div id="results">
        <?php
        $carsLookup = new AdvanceCarsLookup('2021-05-11', '2021-05-12');
        $cars = $carsLookup->find();

        foreach ($cars as $car) {
        ?>
            <div class="card" style="width: 18rem;">
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
