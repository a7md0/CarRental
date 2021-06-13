<?php

/**
 * @var string $successMessage
 * @var CarModel[] $carModels
 * @var bool $isAdding
 * @var Car|unset $car
 * @var array $status
 */
?>

<div class="container" style="max-width: 1080px;">
    <div class="py-5 text-center">
        <h2>Car</h2>
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
                        <form class="g-3 needs-validation" action="<?= $_SERVER['REQUEST_URI'] ?>" method="POST" enctype="multipart/form-data">
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label for="exampleDataList" class="form-label">Car model</label>
                                    <select class="form-select" name="car_model_id" required>
                                        <option value="">Pick car model...</option>
                                        <?php foreach ($carModels as $carModel) { ?>
                                            <option value="<?= $carModel->getCarModelId() ?>" <?= isset($car) && $car->getCarModelId() == $carModel->getCarModelId() ? ' selected' : '' ?>><?= $carModel->getFullDisplayName() ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="license_plate" class="form-label">License plate</label>
                                        <input type="text" class="form-control" name="license_plate" value="<?= isset($car) ? $car->getLicensePlate() : '' ?>" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="vin" class="form-label">Vehicle identification number</label>
                                        <input type="text" class="form-control" name="vin" value="<?= isset($car) ? $car->getVehicleIdentificationNumber() : '' ?>">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="color" class="form-label">Color</label>
                                        <input type="text" class="form-control" name="color" value="<?= isset($car) ? $car->getColor() : '' ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="daily_rent_fees" class="form-label">Daily rent fees</label>
                                        <input type="number" class="form-control" name="daily_rent_fees" min="0.000" step="0.001" value="<?= isset($car) ? $car->getDailyRentRate() : '' ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="daily_rent_fees" class="form-label">Status</label>
                                        <select class="form-select" name="status">
                                            <?php foreach ($status as $key => $value) { ?>
                                                <option value="<?= $key ?>" <?= isset($car) && $car->getStatus() == $key ? ' selected' : '' ?>><?= $value ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <?php if (isset($car) && $car->getPreviewImage() !== null && strlen($car->getPreviewImage()) > 0) { ?>
                                        <img src="<?= $car->getPreviewImage() ?>" class="card-img-top">
                                    <?php } ?>
                                </div>


                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="preview_image" class="form-label">Preview image</label>
                                        <input type="file" class="form-control" name="preview_image" accept="image/*">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-4">
                                        <button type="reset" class="btn btn-danger">Reset</button>
                                        <button type="submit" class="btn btn-primary"><?= $isAdding ? 'Add' : 'Update' ?></button>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </article>
            </div>
        </div>
    </div>
</div>
