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

/** @var CarAccessory[] */
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

        // TODO: Create sales invoice (unpaid)
        $salesInvoice = new SalesInvoice();
        $salesInvoice->setStatus('unpaid')->setPaidAmount(0)->setGrandTotal($cartTotal);
        $salesInvoice->insert();

        // TODO: Create sales invoice items
        $salesInvoiceItems = [];

        foreach ($cartItems as $item) {
            $carInvoiceItem = new SalesInvoiceItem();
            $carInvoiceItem->setSalesInvoiceId($salesInvoice->getSalesInvoiceId())
                ->setItem($item[0])
                ->setPrice($item[2]);

            $salesInvoiceItems[] = $carInvoiceItem;
        }

        SalesInvoiceItem::insertMany($salesInvoiceItems);

        // TODO: Create new user_car_reservation (unconfirmed)
        $userCarReservation = new UserCarReservation();
        $userCarReservation->setUserId($_SESSION['user']['user_id'])
            ->setCarId($carId)
            ->setPickupDate($pickupDateStr)
            ->setReturnDate($returnDateStr)
            ->setStatus('unconfirmed')
            ->setSalesInvoiceId($salesInvoice->getSalesInvoiceId());

        $userCarReservation->insert();


        // TODO: Create new car_reservation_accessory
        $carReservationAccessories = [];

        foreach ($pickedAccessories as $pickedAccessory) {
            $carReservationAccessory = new CarReservationAccessory();
            $carReservationAccessory->setUserCarReservationId($userCarReservation->getUserCarReservationId())
                ->setCarAccessoryId($pickedAccessory->getCarAccessoryId());

            $carReservationAccessories[] = $carReservationAccessory;
        }

        if (count($carReservationAccessories) > 0) {
            CarReservationAccessory::insertMany($carReservationAccessories);
        }

        // TODO: Redirect to confirm reservation page
        header("Location: ?p=confirm-reservation&reservation_id=" . $userCarReservation->getUserCarReservationId());
        exit;
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
