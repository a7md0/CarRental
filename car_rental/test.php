<?php

require_once('include/env.php');
require_once('include/database.class.php');
require_once('include/models/car.model.php');
require_once('include/models/car_accessory.model.php');
require_once('include/models/car_model.model.php');

require_once('include/models/user.model.php');

require_once('include/query/where_clause.class.php');

$car = Car::findById(1);
var_dump($car);

$carAccessory = CarAccessory::findById(100);
var_dump($carAccessory);

$carModel = CarModel::findById(1000);
var_dump($carModel);

$userModel = User::findById(1);
var_dump($userModel);


$where0 = (new WhereClause())->where("x", "123", "=")->whereBetween("y", 1, 5);

echo $where0->getSQL('ON') . "<br />";
print_r($where0->types) . "<br />";
print_r($where0->values) . "<br />";
