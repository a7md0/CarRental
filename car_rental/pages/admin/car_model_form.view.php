<?php

/**
 * @var string $successMessage
 * @var CarModel[] $carModels
 * @var CarType[] $carTypes
 * @var bool $isAdding
 * @var CarModel|unset $carModel
 * @var array $status
 * @var string carModelId
 */
?>

<div class="container" style="max-width: 1080px;">
    <div class="py-5 text-center">
        <h2>Car model form</h2>
    </div>

    <div class="row g-5">
        <div class="col-md-12">
            <?php if (isset($successMessage) && strlen($successMessage) > 0) { ?>
                <div class="alert alert-success" role="alert">
                    <?= $successMessage ?>
                </div>
            <?php } ?>

            <?php if (isset($errorMessage) && strlen($errorMessage) > 0) { ?>
                <div class="alert alert-danger" role="alert">
                    <?= $errorMessage ?>
                </div>
            <?php } ?>


            <div class="card">
                <article class="card-group-item">
                    <div class="card-body m-3">
                        <form class="g-3 needs-validation" action="<?= $_SERVER['REQUEST_URI'] ?>" method="POST">
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label for="exampleDataList" class="form-label">Car model</label>
                                    <select class="form-select" name="car_model_id" onchange="submit_get({ carModelId: this.value })" required>
                                        <option value="">Add new model</option>
                                        <?php foreach ($carModels as $cm) { ?>
                                            <option value="<?= $cm->getCarModelId() ?>" <?= isset($carModel) && $cm->getCarModelId() == $carModel->getCarModelId() ? ' selected' : '' ?>><?= $cm->getFullDisplayName() ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="exampleDataList" class="form-label">Car type</label>
                                    <select class="form-select" name="car_type_id" required>
                                        <option value="">Pick car type...</option>
                                        <?php foreach ($carTypes as $carType) { ?>
                                            <option value="<?= $carType->getCarTypeId() ?>" <?= isset($carModel) && $carType->getCarTypeId() == $carModel->getCarTypeId() ? ' selected' : '' ?>><?= $carType->getType() ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="brand" class="form-label">Brand (manufacturer)</label>
                                        <input type="text" class="form-control" name="brand" value="<?= isset($carModel) ? $carModel->getBrand() : '' ?>" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="model" class="form-label">Model name </label>
                                        <input type="text" class="form-control" name="model" value="<?= isset($carModel) ? $carModel->getModel() : '' ?>">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="year" class="form-label">Manufacture year</label>
                                        <input type="number" class="form-control" name="year" min="1900" value="<?= isset($carModel) ? $carModel->getYear() : '' ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="number_of_seats" class="form-label">Number of seats</label>
                                        <input type="text" class="form-control" name="number_of_seats" value="<?= isset($carModel) ? $carModel->getNumberOfSeats() : '' ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4 text-center">
                                <button type="reset" class="btn btn-danger">Reset</button>
                                <button type="submit" class="btn btn-primary"><?= $isAdding ? 'Add' : 'Update' ?></button>
                            </div>

                        </form>
                    </div>
                </article>
            </div>
        </div>
    </div>
</div>
