<?php

$first_day_this_month = date('Y-m-01'); // hard-coded '01' for first day // https://stackoverflow.com/a/3321973/1738413
$last_day_this_month  = date('Y-m-t'); // https://stackoverflow.com/a/3321973/1738413

$from = $first_day_this_month;
$to = $last_day_this_month;

if (isset($_GET['from'])) {
    $from = $_GET['from'];
}

if (isset($_GET['to'])) {
    $to = $_GET['to'];
}

$whereClause = new WhereClause();
$whereClause->whereBetween('created_at', "$from 00:00:00", "$to 23:59:59")
->where('status', 'cancelled', '!=');

list($paidAmount, $totalAmount) = SalesInvoice::aggregateValues(['SUM' => ['paid_amount', 'grand_total']], $whereClause);
$owedAmount = $totalAmount - $paidAmount;

/***** Pass values to the view *****/
$VALUES['paidAmount'] = $paidAmount ?? 0;
$VALUES['totalAmount'] = $totalAmount ?? 0;
$VALUES['owedAmount'] = $owedAmount;
$VALUES['from'] = $from;
$VALUES['to'] = $to;
/******************************** */
