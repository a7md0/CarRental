<?php

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

    $carDetails = CarDetail::findById($reservation->getCarId());

    $salesInvoice = SalesInvoice::findById($reservation->getSalesInvoiceId());

    $salesInvoiceItemWhereClause = (new WhereClause())->where('sales_invoice_id', $reservation->getSalesInvoiceId());
    $salesInvoiceItems = SalesInvoiceItem::find($salesInvoiceItemWhereClause);

    $paidAmount = $salesInvoice->getPaidAmount();
    $totalAmount = $salesInvoice->getGrandTotal() - $paidAmount;
}

$VALUES += [
    'successMessage' => $successMessage,
    'reservation' => $reservation,
    'carDetails' => $carDetails,
    'salesInvoice' => $salesInvoice,
    'salesInvoiceItems' => $salesInvoiceItems,
    'paidAmount' => $paidAmount,
    'totalAmount' => $totalAmount,
];
