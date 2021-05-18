<?php

require_once('include/env.php');
require_once('include/database.class.php');
require_once('include/models/car.model.php');
require_once('include/models/car_accessory.model.php');
require_once('include/models/car_model.model.php');

require_once('include/models/user.model.php');

$car = Car::findOne(1);
var_dump($car);

$carAccessory = CarAccessory::findOne(100);
var_dump($carAccessory);

$carModel = CarModel::findOne(1000);
var_dump($carModel);

$userModel = User::findOne(1);
var_dump($userModel);
