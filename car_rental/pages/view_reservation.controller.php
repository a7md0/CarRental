<?php

if (isset($_GET['reservationCode'])) {
    $reservationCode = $_GET['reservationCode'];
    $source = isset($_GET['from']) ? $_GET['from'] : '';

    $whereClause = new WhereClause();
    $whereClause->where('reservation_code', $reservationCode)
        ->where('user_id', $CURRENT_USER->getUserId());

    $reservation = UserCarReservation::findOne($whereClause);

    $canAmend = false;
    $cannotAmendMessage = '';
    $canCancel = false;
    $cannotCancelMessage = '';

    if ($reservation !== null) {
        $canAmend = $reservation->canAmend($cannotAmendMessage);
        $canCancel = $reservation->canCancel($cannotCancelMessage);

        if ($source == 'checkout') {
            $VALUES['successMessage'] = 'Your reservation have been confirmed successfully.';
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['amendReservation']) && $_POST['amendReservation'] == 'true' && $canAmend) {
            $pickupDate = date_create($_POST['pickup_date']);
            $returnDate = date_create($_POST['return_date']);
            if ($returnDate < $pickupDate) {
                $VALUES['errorMessage'] = 'Return date should not be before the pickup date!';
            } else {
                $car = (new Car())->setCarId($reservation->getCarId());
                $isCarReserved = $car->isReservedExcept($reservation->getUserCarReservationId(), $_POST['pickup_date'], $_POST['return_date']);

                if ($isCarReserved === false) {
                    $reservation->amend($_POST['pickup_date'], $_POST['return_date'], $amendError);

                    if (isset($amendError)) {
                        $VALUES['errorMessage'] = $amendError;
                    } else {
                        $reservation = UserCarReservation::findOne($whereClause);
                        $VALUES['successMessage'] = 'Your reservation have been amended successfully.';
                    }
                } else {
                    $VALUES['errorMessage'] = "The car is not available between {$_POST['pickup_date']} and {$_POST['return_date']}";
                }
            }
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancelReservation']) && $_POST['cancelReservation'] == 'true' && $canCancel) {
            $reservation->cancel($cancelError);

            if (isset($cancelError)) {
                $VALUES['errorMessage'] = $cancelError;
            } else {
                $VALUES['successMessage'] = 'Your reservation have been cancelled successfully, the amount have been refunded to the original payment method.';
                $reservation = UserCarReservation::findOne($whereClause);
            }
        }

        if ($reservation->getStatus() == 'unconfirmed') {
            $VALUES['infoMessages'][] = "This reservation is unconfirmed, make sure to pay the outstanding balance to confirm it.<br />";
        }

        if ($reservation->getStatus() == 'cancelled') {
            $VALUES['infoMessages'][] = "This reservation is cancelled.<br />";
        }

        $carDetails = CarDetail::findById($reservation->getCarId());

        $salesInvoice = SalesInvoice::findById($reservation->getSalesInvoiceId());

        $salesInvoiceItemWhereClause = (new WhereClause())->where('sales_invoice_id', $reservation->getSalesInvoiceId());
        $salesInvoiceItems = SalesInvoiceItem::find($salesInvoiceItemWhereClause);

        $accessoriesWhereClause = (new WhereClause())->where('user_car_reservation_id', $reservation->getUserCarReservationId());
        $accessories = CarAccessory::findJoined('car_reservation_accessory', 'car_accessory_id', $accessoriesWhereClause);

        $canAmend = $reservation->canAmend($cannotAmendMessage);
        $canCancel = $reservation->canCancel($cannotCancelMessage);

        $paidAmount = $salesInvoice->getPaidAmount();
        $totalAmount = $salesInvoice->getGrandTotal();
        $dueAmount = $totalAmount - $paidAmount;

        if ($dueAmount > 0.000 && $reservation->getStatus() != 'cancelled') {
            $VALUES['infoMessages'][] = "You have an outstanding balance of BD$dueAmount, please <a href=\"?p=checkout&reservationCode={$reservation->getReservationCode()}\">click here</a> to pay now.<br />";
        }

        $VALUES['reservation'] = $reservation;
        $VALUES['carDetails'] = $carDetails;

        $VALUES['salesInvoice'] = $salesInvoice;
        $VALUES['accessories'] = $accessories;

        $VALUES['paidAmount'] = $paidAmount;
        $VALUES['totalAmount'] = $totalAmount;
        $VALUES['dueAmount'] = $dueAmount;

        $VALUES['cannotAmendMessage'] = $cannotAmendMessage;
        $VALUES['cannotCancelMessage'] = $cannotCancelMessage;
    } else {
        $VALUES['warningMessage'] = "No matching reservation is found!";
    }

    $VALUES['reservationCode'] = $reservationCode;

    $VALUES['canAmend'] = $canAmend;
    $VALUES['canCancel'] = $canCancel;
}
