<?php

$carId = intval($_GET['carId']);
$pickupDate = date_create($_GET['pickupDate']);
$returnDate = date_create($_GET['returnDate']);

$pickupDateStr = date_format($pickupDate, 'Y-m-d');
$returnDateStr = date_format($returnDate, 'Y-m-d');

$reservationDays = $pickupDate->diff($returnDate)->days + 1;

$car = Car::findById($carId);
$carModel = $car->getCarModel();

$whereAccessories = new WhereClause();
$whereAccessories->where('car_type_id', $carModel->getCarTypeId());

$cartItems = [];
$cartTotal = 0.000;

$cost = floatval($car->getDailyRentRate()) * $reservationDays;
$cartItems[] = ["Car rent", "$reservationDays days", $cost];
$cartTotal += $cost;

/** @var CarAccessory[] */
$accessories = [];
foreach (CarAccessory::find($whereAccessories) as $carAccessory) {
    $id = $carAccessory->getCarAccessoryId();
    $accessories[$id] = $carAccessory;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['accessory_id'])) {
        $accessoryId = intval($_POST['accessory_id']);

        if (!isset($_SESSION['place_reservation'][$carId]['picked_accessories']) || !is_array($_SESSION['place_reservation'][$carId]['picked_accessories'])) {
            $_SESSION['place_reservation'][$carId]['picked_accessories'] = [];
        }

        if (!in_array($accessoryId, $_SESSION['place_reservation'][$carId]['picked_accessories'])) {
            $_SESSION['place_reservation'][$carId]['picked_accessories'][] = $accessoryId;
        }
    } else if (isset($_POST['cancel'])) {
        unset($_SESSION['place_reservation'][$carId]);
        header("Location: ?p=lookup-cars");
        exit;
    }
}

$pickedAccessories = [];
$sessionPickedAccessories = $_SESSION['place_reservation'][$carId]['picked_accessories'] ?? [];
foreach ($sessionPickedAccessories as $pickedAccessory) {
    $pickedAccessories[$pickedAccessory] = $accessories[$pickedAccessory];

    $cost = floatval($accessories[$pickedAccessory]->getCharge());
    $cartItems[] = [$accessories[$pickedAccessory]->getName(), "",  $cost];
    $cartTotal +=  $cost;
}
$availableAccessories = array_diff_key($accessories, $pickedAccessories);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['place_reservation'])) {
        // TODO: Check if var is available between pickup&return dates
        // TODO: Create new user_car_reservation (unconfirmed)
        // TODO: Create new car_reservation_accessory
        // TODO: Create sales invoice (unpaid)
        // TODO: Create sales invoice item

        // TODO: Redirect to confirm reservation page
    }
}

$VALUES += [
    'pickupDate' => $pickupDate,
    'returnDate' => $returnDate,

    'pickupDateStr' => $pickupDateStr,
    'returnDateStr' => $returnDateStr,

    'car' => $car,
    'accessories' => $accessories,

    'pickedAccessories' => $pickedAccessories,
    'availableAccessories' => $availableAccessories,

    'cartItems' => $cartItems,
    'cartTotal' => $cartTotal,
];

// var_dump($accessories);
// var_dump($pickedAccessories);
// var_dump($availableAccessories);
