<?php

$CURRENT_PATH = dirname(__FILE__);
$DIRECTORY_SEPARATOR = DIRECTORY_SEPARATOR;

$AUTO_LOADER_MAP = [
    'CarAccessory' => 'models/car_accessory.model.php',
    'CarModel' => 'models/car_model.model.php',
    'CarReservationAccessory' => 'models/car_reservation_accessory.model.php',
    'CarType' => 'models/car_type.model.php',
    'Car' => 'models/car.model.php',
    'Model' => 'models/model.class.php',
    'SalesInvoiceItem' => 'models/sales_invoice_item.model.php',
    'SalesInvoice' => 'models/sales_invoice.model.php',
    'Transaction' => 'models/transaction.model.php',
    'UserAddress' => 'models/user_address.model.php',
    'UserCarReservation' => 'models/user_car_reservation.model.php',
    'UserType' => 'models/user_type.model.php',
    'User' => 'models/user.model.php',

    'InsertClause' => 'query/insert_clause.class.php',
    'SetClause' => 'query/set_clause.class.php',
    'WhereClause' => 'query/where_clause.class.php',

    'Database' => 'database.class.php',
    'HTTP2' => 'HTTP2.php',

    'Route' => 'route.class.php',
    'AuthorizedOnlyRoute' => 'route.class.php',
    'AdminOnlyRoute' => 'route.class.php',
    'UnauthorizedOnlyRoute' => 'route.class.php',
    'ErrorRoute' => 'route.class.php',

    'AvailableCarsLookup' => 'custom/available_cars_lookup.class.php',
    'AdvancedCarsLookup' => 'custom/advanced_cars_lookup.class.php',

    'Pagination' => 'query/pagination.trait.php',
    'Upload' => 'upload.class.php',

    'CarDetail' => 'custom/car_details.model.php',

    // '' => '',
    // '' => '',
];

spl_autoload_register(function ($class_name) {
    global $CURRENT_PATH, $DIRECTORY_SEPARATOR;
    global $AUTO_LOADER_MAP;

    if (array_key_exists($class_name, $AUTO_LOADER_MAP)) {
        $script = "{$CURRENT_PATH}{$DIRECTORY_SEPARATOR}{$AUTO_LOADER_MAP[$class_name]}"; // Construct the absolute path (current auto_loader path + / + the predefined script path)

        require_once $script;
    }
});
