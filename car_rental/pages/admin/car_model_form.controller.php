<?php

$carModels = CarModel::find();
$carTypes = CarType::find();
$isAdding = true;

if (isset($_GET['successMessage'])) {
    $VALUES['successMessage'] = $_GET['successMessage'];
}

if (isset($_GET['carModelId']) && !empty($_GET['carModelId'])) {
    $carModelId = intval($_GET['carModelId']);
    $carModel = CarModel::findById($carModelId);

    if ($carModel != null) {
        $isAdding = false;
        $VALUES['carModel'] = $carModel;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // TODO: Validate inputs

            $carModel->setCarTypeId($_POST['car_type_id'])
                ->setBrand($_POST['brand'])
                ->setModel($_POST['model'])
                ->setYear($_POST['year'])
                ->setNumberOfSeats($_POST['number_of_seats']);

            if ($carModel->update()) {
                header("Location: ?p=car-model-form&carModelId={$carModel->getCarModelId()}&successMessage=The%20car%20details%20have%20been%20updated%20successfully");
                exit;
            } else {
                $VALUES['errorMessage'] = 'Failed to update the car details, please contact the technical support.';
            }
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $carModel = new CarModel();

    $carModel->setCarTypeId($_POST['car_type_id'])
        ->setBrand($_POST['brand'])
        ->setModel($_POST['model'])
        ->setYear($_POST['year'])
        ->setNumberOfSeats($_POST['number_of_seats']);

    if ($carModel->insert()) {
        header("Location: ?p=car-model-form&carModelId={$carModel->getCarModelId()}&successMessage=The%20car%20details%20have%20been%20added%20successfully");
        exit;
    } else {
        $VALUES['errorMessage'] = 'Failed to update the car details, please contact the technical support.';
    }
}


$VALUES['carModels'] = $carModels;
$VALUES['carTypes'] = $carTypes;
$VALUES['isAdding'] = $isAdding;
$VALUES['carModelId'] = $_GET['carModelId'] ?? '';
$VALUES['status'] = [
    'available' => 'Available',
    'unavailable' => 'Unavailable',
    'servicing' => 'Servicing',
    'repairing' => 'Repairing',
    'sold' => 'Sold',
    'destroyed' => 'Destroyed',
    'stolen' => 'Stolen'
];
