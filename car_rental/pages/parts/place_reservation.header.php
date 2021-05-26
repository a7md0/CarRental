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

$pickedAccessories = [];
foreach ($_SESSION['place_reservation']['picked_accessories'] ?? [] as $pickedAccessory) {
    $pickedAccessories[] = $accessories[$pickedAccessory];

    $cost = floatval($accessories[$pickedAccessory]->getCharge());
    $cartItems[] = [$accessories[$pickedAccessory]->getName(), "",  $cost];
    $cartTotal +=  $cost;
}
$availableAccessories = array_diff_key($accessories, $pickedAccessories);

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
