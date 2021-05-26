<?php

require_once 'env.php';

if (ENABLE_APP_DEBUG === true) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

require_once 'database.class.php';
require_once 'authentication.php';
