<?php

require_once('include/env.php');
require_once('include/auto_loader.php');


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

// $cars = Car::find();
// var_dump($cars);

$cars = Car::count();
var_dump($cars);

var_dump(CarModel::aggregateValues(['MIN' => ['year', 'number_of_seats'], 'MAX' => ['year', 'number_of_seats']]));


// $where0 = (new WhereClause("C"))->where("x", "123", "=")->whereBetween("y", 1, 5)->whereFullText(['col1', 'col2'], 'query str');

// echo $where0->getSQL('ON') . "<br />";
// echo $where0->getTypes() . "<br />";
// print_r($where0->getValues()) . "<br />";

// $set1 = new SetClause($car->values);
// echo $set1->getSQL() . "<br />";
// echo $set1->getTypes() . "<br />";
// print_r($set1->getValues()) . "<br />";
