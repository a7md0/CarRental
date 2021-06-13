<?php

$CUSTOM_CLASSES['main'][] = 'form-signin';
$HIDE_FOOTER = true;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $CURRENT_USER === null && isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $requiredFields = ['email', 'password'];
    foreach ($requiredFields as $requiredField) {
        if (!isset($_POST[$requiredField]) || empty($_POST[$requiredField])) {
            $encodedMessage = urlencode("$requiredField is required.");
            header("Location: ?p=sign-in&errorMessage={$encodedMessage}");
            exit;
        }
    }

    $whereClause = new WhereClause();
    $whereClause->where('email', $email);

    $signInUser = User::findOne($whereClause);
    if ($signInUser !== null) {
        if (password_verify($password, $signInUser->getPassword())) {
            $_SESSION['user']['user_id'] = $signInUser->getUserId();
            $_SESSION['user']['user_type_id'] = $signInUser->getUserTypeId();

            header("Location: ?p=home");
            exit;
        } else {
            http_response_code(401);

            $encodedMessage = urlencode("The provided password is incorrect.");
            header("Location: ?p=sign-in&errorMessage={$encodedMessage}");
            exit;
        }
    } else {
        http_response_code(401);

        $encodedMessage = urlencode("No user was found matching this email address.");
        header("Location: ?p=sign-in&errorMessage={$encodedMessage}");
        exit;
    }
}

?>

<style>
    html,
    body {
        height: 100%;
    }

    body {
        display: flex;
        align-items: center;
        padding-top: 40px;
        padding-bottom: 40px;
        background-color: #f5f5f5;
    }

    .form-signin {
        width: 100%;
        max-width: 330px;
        padding: 15px;
        margin: auto;
    }

    .form-signin .checkbox {
        font-weight: 400;
    }

    .form-signin .form-floating:focus-within {
        z-index: 2;
    }

    .form-signin input[type="email"] {
        margin-bottom: -1px;
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 0;
    }

    .form-signin input[type="password"] {
        margin-bottom: 10px;
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }
</style>
