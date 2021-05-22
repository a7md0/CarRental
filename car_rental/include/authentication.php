<?php

session_name('CrsSession');
session_start();

/** @var User|null */
$CURRENT_USER = null;
if (isset($_SESSION['user'])) {
    $user_id = $_SESSION['user']['user_id'];

    $CURRENT_USER = User::findById($user_id);
}
