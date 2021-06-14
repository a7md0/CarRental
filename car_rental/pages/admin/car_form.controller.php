<?php

$carModels = CarModel::find();
$isAdding = true;

function handleUpload(Car $c)
{
    if (isset($_FILES['preview_image']) && $_FILES['preview_image']['size'] > 0) {
        try {
            $upload = new Upload('assets/images/upload/');
            $file = $upload->upload($_FILES['preview_image']);

            $c->setPreviewImage($file->getPath());
        } catch (Exception $ex) {
            echo '<script>alert(' . $ex->getMessage() . ');</script>';
        }
    }
}

if (isset($_GET['successMessage'])) {
    $VALUES['successMessage'] = $_GET['successMessage'];
}

if (isset($_GET['carId'])) {
    $carId = intval($_GET['carId']);
    $car = Car::findById($carId);

    if ($car !== null) {
        $isAdding = false;
        $VALUES['car'] = $car;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validate inputs
            $valid = true;

            $requiredFields = ['car_model_id', 'license_plate', 'color', 'daily_rent_fees', 'status'];
            foreach ($requiredFields as $requiredField) {
                if (!isset($_POST[$requiredField]) || empty($_POST[$requiredField])) {
                    $VALUES['errorMessage'] = "$requiredField is required.";
                    $valid = false;
                    break;
                }
            }

            if ($valid) {
                handleUpload($car);

                $car->setCarModelId($_POST['car_model_id'])
                    ->setLicensePlate($_POST['license_plate'])
                    ->setVehicleIdentificationNumber($_POST['vin'])
                    ->setColor($_POST['color'])
                    ->setDailyRentRate($_POST['daily_rent_fees'])
                    ->setStatus($_POST['status']);

                if ($car->update()) {
                    header("Location: ?p=car-form&carId={$car->getCarId()}&successMessage=The%20car%20details%20have%20been%20updated%20successfully");
                    exit;
                } else {
                    $VALUES['errorMessage'] = 'Failed to update the car details, please contact the technical support.';
                }
            }
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $car = new Car();

    // Validate inputs
    $valid = true;

    $requiredFields = ['car_model_id', 'license_plate', 'color', 'daily_rent_fees', 'status'];
    foreach ($requiredFields as $requiredField) {
        if (!isset($_POST[$requiredField]) || empty($_POST[$requiredField])) {
            $VALUES['errorMessage'] = "$requiredField is required.";
            $valid = false;
            break;
        }
    }

    if ($valid) {
        handleUpload($car);

        $car->setCarModelId($_POST['car_model_id'])
            ->setLicensePlate($_POST['license_plate'])
            ->setVehicleIdentificationNumber($_POST['vin'])
            ->setColor($_POST['color'])
            ->setDailyRentRate($_POST['daily_rent_fees'])
            ->setStatus($_POST['status']);


        if ($car->insert()) {
            header("Location: ?p=car-form&carId={$car->getCarId()}&successMessage=The%20car%20details%20have%20been%20added%20successfully");
            exit;
        } else {
            $VALUES['errorMessage'] = 'Failed to update the car details, please contact the technical support.';
        }
    }
}


$VALUES['carModels'] = $carModels;
$VALUES['isAdding'] = $isAdding;
$VALUES['status'] = [
    'available' => 'Available',
    'unavailable' => 'Unavailable',
    'servicing' => 'Servicing',
    'repairing' => 'Repairing',
    'sold' => 'Sold',
    'destroyed' => 'Destroyed',
    'stolen' => 'Stolen'
];
