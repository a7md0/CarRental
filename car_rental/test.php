<?php

require_once('include/env.php');
require_once('include/database.class.php');
require_once('include/models/car.model.php');
require_once('include/models/car_accessory.model.php');

$car = Car::findOne(1);
var_dump($car);

$carAccessory = CarAccessory::findOne(100);
var_dump($carAccessory);
