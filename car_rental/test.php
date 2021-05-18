<?php

require_once('include/env.php');
require_once('include/database.class.php');
require_once('include/models/car.model.php');

$car = Car::findOne(1);
var_dump($car);
