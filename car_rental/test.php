<?php

require_once('include/env.php');
require_once('include/database.class.php');

require_once('include/models/car.model.php');
require_once('include/models/car_accessory.model.php');
require_once('include/models/car_model.model.php');

require_once('include/models/user.model.php');

require_once('include/query/where_clause.class.php');
require_once('include/query/set_clause.class.php');
require_once('include/query/insert_clause.class.php');

$car = Car::findById(1);
var_dump($car);

// var_dump($car->insert());
// var_dump($car->delete());


$carAccessory = CarAccessory::findById(100);
var_dump($carAccessory);

$carModel = CarModel::findById(1000);
var_dump($carModel);

$userModel = User::findById(1);
var_dump($userModel);

$cars = Car::find();
var_dump($cars);

$cars = Car::count();
var_dump($cars);


// $where0 = (new WhereClause())->where("x", "123", "=")->whereBetween("y", 1, 5);

// echo $where0->getSQL('ON') . "<br />";
// echo $where0->getTypes() . "<br />";
// print_r($where0->getValues()) . "<br />";

// $set1 = new SetClause($car->values);
// echo $set1->getSQL() . "<br />";
// echo $set1->getTypes() . "<br />";
// print_r($set1->getValues()) . "<br />";
