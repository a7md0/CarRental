<?php

if (isset($_GET['reservationCode'])) {
    $reservationCode = intval($_GET['reservationCode']);
    $source = isset($_GET['from']) ? $_GET['from'] : '';

    $whereClause = new WhereClause();
    $whereClause->where('reservation_code', $reservationCode)
        ->where('user_id', $CURRENT_USER->getUserId());

    $reservation = UserCarReservation::findOne($whereClause);

    $successMessage = '';

    if ($reservation != null) {
        if ($source == 'checkout') {
            $successMessage = "Your reservation have been confirmed successfully.";
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancelReservation']) && $_POST['cancelReservation'] == 'true' && $reservation->getStatus() != 'cancelled') {
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
    ];
}