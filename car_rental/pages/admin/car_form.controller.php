<?php

$carModels = CarModel::find();
$isAdding = false;

if (isset($_GET['carId'])) {
    $carId = intval($_GET['carId']);
    $car = Car::findById($carId);

    if ($car != null) {
        $VALUES['car'] = $car;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // TODO: Validate inputs

            if (isset($_FILES['preview_image']) && $_FILES['preview_image']['size'] > 0) {
                try {
                    $upload = new Upload('assets/images/upload/');
                    $file = $upload->upload($_FILES['preview_image']);

                    $car->setPreviewImage($file->getPath());
                } catch (Exception $ex) {
                    // echo $ex->getMessage();
                    // TODO: Show upload image error
                }
            }

            $car->setCarModelId($_POST['car_model_id'])
                ->setLicensePlate($_POST['license_plate'])
                ->setVehicleIdentificationNumber($_POST['vin'])
                ->setColor($_POST['color'])
                ->setDailyRentRate($_POST['daily_rent_fees'])
                ->setStatus($_POST['status']);

            $car->update();
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
