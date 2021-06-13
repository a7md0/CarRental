<?php

$reservationCode = $_GET['reservationCode'];
$source = isset($_GET['from']) ? $_GET['from'] : '';

$whereClause = new WhereClause();
$whereClause->where('reservation_code', $reservationCode)
    ->where('user_id', $CURRENT_USER->getUserId());

$reservation = UserCarReservation::findOne($whereClause);

$paidAmount = 0.000;
$totalAmount = 0.000;
$successMessage = '';

if ($reservation !== null) {
    if ($source == 'place-reservation') {
        $successMessage = "Your reservation #{$reservationCode} have been placed successfully. The reservation is still unconfirmed, please complete the checkout process to confirm your reservation.";
    }

    $salesInvoice = SalesInvoice::findById($reservation->getSalesInvoiceId());

    $salesInvoiceItemWhereClause = (new WhereClause())->where('sales_invoice_id', $reservation->getSalesInvoiceId());
    $salesInvoiceItems = SalesInvoiceItem::find($salesInvoiceItemWhereClause);

    $paidAmount = $salesInvoice->getPaidAmount();
    $totalAmount = $salesInvoice->getGrandTotal() - $paidAmount;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $userAddress = new UserAddress();
        $userAddress->setUserId($CURRENT_USER->getUserId())
            ->setType('billing')
            ->setAddress1($_POST['address1'])
            ->setAddress2($_POST['address2'])
            ->setCountry($_POST['country'])
            ->setCity($_POST['city'])
            ->setZipCode($_POST['zip']);

        $userAddress->insert();

        $transaction = new Transaction();
        $transaction->setSalesInvoiceId($reservation->getSalesInvoiceId())
            ->setUserAddressId($userAddress->getUserAddressId())
            ->setAmount($totalAmount)
            ->setMethod('Credit-card')
            ->setStatus('completed');

        $transaction->insert();

        $reservation->setStatus('confirmed')->update();
        $salesInvoice->setStatus('paid')->increasePaidAmount($totalAmount)->update();

        header("Location: ?p=view-reservation&reservationCode={$reservation->getReservationCode()}&from=checkout");
        exit;
    }
}

$VALUES += [
    'successMessage' => $successMessage,
    'reservation' => $reservation,
    'salesInvoice' => $salesInvoice,
    'salesInvoiceItems' => $salesInvoiceItems,
    'paidAmount' => $paidAmount,
    'totalAmount' => $totalAmount,
];
