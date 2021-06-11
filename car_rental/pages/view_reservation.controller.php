<?php

if (isset($_GET['reservationCode'])) {
    $reservationCode = intval($_GET['reservationCode']);
    $source = isset($_GET['from']) ? $_GET['from'] : '';

    $whereClause = new WhereClause();
    $whereClause->where('reservation_code', $reservationCode)
        ->where('user_id', $CURRENT_USER->getUserId());

    $reservation = UserCarReservation::findOne($whereClause);

    $successMessage = '';
    $canAmend = false;
    $cannotAmendMessage = '';
    $canCancel = false;

    if ($reservation != null) {
        $canAmend = $reservation->canAmend($cannotAmendMessage);
        $canCancel = $reservation->canCancel();

        if ($source == 'checkout') {
            $successMessage = "Your reservation have been confirmed successfully.";
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['amendReservation']) && $_POST['amendReservation'] == 'true' && $canAmend) {
            $pickupDate = date_create($_POST['pickup_date']);
            $returnDate = date_create($_POST['return_date']);
            if ($rd < $pd) {
                $VALUES['errorMessage'] = 'Return date should not be before the pickup date!';
            } else {
                $wasAmended = $reservation->amend($_POST['pickup_date'], $_POST['return_date'], $amendError);

                if ($wasAmended) {
                    $reservation = UserCarReservation::findOne($whereClause);
                    $successMessage = 'Your reservation have been amended successfully, ???.';
                } else {
                    $VALUES['errorMessage'] = $amendError;
                }
            }

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancelReservation']) && $_POST['cancelReservation'] == 'true' && $canCancel) {
            $reservation->cancel();

            $successMessage = 'Your reservation have been cancelled successfully, the amount have been refunded to the original payment method.';
            $reservation = UserCarReservation::findOne($whereClause);
        }

        $carDetails = CarDetail::findById($reservation->getCarId());

        $salesInvoice = SalesInvoice::findById($reservation->getSalesInvoiceId());

        $salesInvoiceItemWhereClause = (new WhereClause())->where('sales_invoice_id', $reservation->getSalesInvoiceId());
        $salesInvoiceItems = SalesInvoiceItem::find($salesInvoiceItemWhereClause);

        $accessoriesWhereClause = (new WhereClause())->where('user_car_reservation_id', $reservation->getUserCarReservationId());
        $accessories = CarAccessory::findJoined('car_reservation_accessory', 'car_accessory_id', $accessoriesWhereClause);

        $paidAmount = $salesInvoice->getPaidAmount();
        $totalAmount = $salesInvoice->getGrandTotal();
        $dueAmount = $totalAmount - $paidAmount;
    }

    $VALUES += [
        'reservationCode' => $reservationCode,
        'successMessage' => $successMessage,
        'reservation' => $reservation,
        'carDetails' => $carDetails,
        'salesInvoice' => $salesInvoice,
        'accessories' => $accessories,
        'paidAmount' => $paidAmount,
        'totalAmount' => $totalAmount,
        'dueAmount' => $dueAmount,
        'canAmend' => $canAmend,
        'cannotAmendMessage' => $cannotAmendMessage,
        'canCancel' => $canCancel,
    ];
}
