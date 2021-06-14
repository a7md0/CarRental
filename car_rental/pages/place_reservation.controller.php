<?php

/**
 * @var User $CURRENT_USER
 */

if (
    !isset($_GET['pickupDate']) ||
    !isset($_GET['returnDate']) ||
    empty($_GET['pickupDate']) ||
    empty($_GET['returnDate'])
) {
    header("Location: ?p=lookup-cars");
    exit;
}

$pickupDate = date_create($_GET['pickupDate']);
$returnDate = date_create($_GET['returnDate']);

if (
    !isset($_GET['carId']) ||
    !is_numeric($_GET['carId']) ||
    $pickupDate === false ||
    $returnDate === false ||
    $pickupDate > $returnDate
) {
    header("Location: ?p=lookup-cars");
    exit;
}

$carId = intval($_GET['carId']);

$pickupDateStr = date_format($pickupDate, 'Y-m-d');
$returnDateStr = date_format($returnDate, 'Y-m-d');

$reservationDays = $pickupDate->diff($returnDate)->days + 1;

$car = Car::findById($carId);

if (is_null($car) || $car->isReserved($pickupDateStr, $returnDateStr)) { // Check if var is available between pickup&return dates
    header("Location: ?p=lookup-cars");
    exit;
}

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
    } else if (isset($_POST['deleteAccessory']) && is_numeric($_POST['deleteAccessory'])) {
        if (($key = array_search($_POST['deleteAccessory'], $_SESSION['place_reservation'][$carId]['picked_accessories'])) !== false) {
            unset($_SESSION['place_reservation'][$carId]['picked_accessories'][$key]);
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
        // Create sales invoice (unpaid)
        $salesInvoice = new SalesInvoice();
        $salesInvoice->setStatus('unpaid')->setPaidAmount(0)->setGrandTotal($cartTotal);
        $salesInvoice->insert();

        // Create sales invoice items
        $salesInvoiceItems = [];

        foreach ($cartItems as $item) {
            $carInvoiceItem = new SalesInvoiceItem();
            $carInvoiceItem->setSalesInvoiceId($salesInvoice->getSalesInvoiceId())
                ->setItem($item[0])
                ->setPrice($item[2]);

            $salesInvoiceItems[] = $carInvoiceItem;
        }

        SalesInvoiceItem::insertMany($salesInvoiceItems);

        // Create new user_car_reservation (unconfirmed)
        $reservationCode = uniqid();

        $userCarReservation = new UserCarReservation();
        $userCarReservation->setUserId($_SESSION['user']['user_id'])
            ->setCarId($carId)
            ->setReservationCode($reservationCode)
            ->setPickupDate($pickupDateStr)
            ->setReturnDate($returnDateStr)
            ->setStatus('unconfirmed')
            ->setSalesInvoiceId($salesInvoice->getSalesInvoiceId());

        $userCarReservation->insert();


        // Create new car_reservation_accessory
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

        $to = $CURRENT_USER->getEmail(); // Get current user email address
        $subject = "New reservation placed | " . $reservationCode;
        $body = "Your new reservation has been placed. You could view/amend/cancel your reservation using the code " . $reservationCode;

        mail($to, $subject, $body); // Send email to the user

        // Redirect to confirm reservation page
        header("Location: ?p=checkout&reservationCode={$userCarReservation->getReservationCode()}&from=place-reservation");
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
