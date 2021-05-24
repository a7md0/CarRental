<?php

$AUTO_LOADER_MAP = [
    'CarAccessory' => 'include/models/car_accessory.model.php',
    'CarModel' => 'include/models/car_model.model.php',
    'CarReservationAccessory' => 'include/models/car_reservation_accessory.model.php',
    'CarType' => 'include/models/car_type.model.php',
    'Car' => 'include/models/car.model.php',
    'Model' => 'include/models/model.class.php',
    'SalesInvoiceItem' => 'include/models/sales_invoice_item.model.php',
    'SalesInvoice' => 'include/models/sales_invoice.model.php',
    'Transaction' => 'include/models/transaction.model.php',
    'UserAddress' => 'include/models/user_address.model.php',
    'UserCarReservation' => 'include/models/user_car_reservation.model.php',
    'UserType' => 'include/models/user_type.model.php',
    'User' => 'include/models/user.model.php',

    'InsertClause' => 'include/query/insert_clause.class.php',
    'SetClause' => 'include/query/set_clause.class.php',
    'WhereClause' => 'include/query/where_clause.class.php',

    'Database' => 'include/database.class.php',
    'HTTP2' => 'include/HTTP2.php',

    'Route' => 'include/route.class.php',
    'AuthorizedOnlyRoute' => 'include/route.class.php',
    'AdminOnlyRoute' => 'include/route.class.php',
    'UnauthorizedOnlyRoute' => 'include/route.class.php',
    'ErrorRoute' => 'include/route.class.php',

    'AdvanceCarsLookup' => 'include/models/advance_cars_lookup.class.php',

    'Pagination' => 'include/query/pagination.trait.php',

    // '' => '',
    // '' => '',
];

spl_autoload_register(function ($class_name) {
    global $AUTO_LOADER_MAP;

    if (array_key_exists($class_name, $AUTO_LOADER_MAP)) {
        require_once $AUTO_LOADER_MAP[$class_name];
    }
});
