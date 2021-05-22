<?php

session_name('CrsSession');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['logout']) && isset($_SESSION['user'])) {
    session_unset();
    session_destroy();

    header('Location: ?p=home');

    exit;
}

/** @var User|null */
$CURRENT_USER = null;
if (isset($_SESSION['user'])) {
    try {
        $user_id = $_SESSION['user']['user_id'];

        $CURRENT_USER = User::findById($user_id);
    } catch (Exception $e) {
        session_unset();
        session_destroy();
    }
}

