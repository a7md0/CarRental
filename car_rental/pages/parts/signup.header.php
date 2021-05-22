<?php

$CUSTOM_CLASSES['main'][] = 'form-signup';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $CURRENT_USER == null) {
    $first_name = $_POST['first_name']; // >2 char
    $last_name = $_POST['last_name']; // >2 char
    $email = $_POST['email']; // valid mail
    $password = $_POST['password']; // pw req?
    $cpr = $_POST['cpr']; // 9 digits
    $nationality = $_POST['nationality']; // Not empty
    $phone = $_POST['phone']; // 8 digits
    $gender = $_POST['gender']; // Male or female

    // TODO: Input validations

    // TODO: Validate email is not already existing

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $user = new User();
    $user->setUserTypeId(1)
            ->setFirstName($first_name)
            ->setLastName($last_name)
            ->setEmail($email)
            ->setPassword($hashed_password)
            ->setCpr($cpr)
            ->setNationality($nationality)
            ->setPhone($phone)
            ->setGender($gender);

    if ($user->insert()) {
        $_SESSION['user']['user_id'] = $user->getUserId();
        $_SESSION['user']['user_type_id'] = $user->getUserTypeId();

        header('Location: ?p=home');
        exit;
    } else {
        $message .= "Failed to register, please contact the support.";
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

    .form-signup {
        width: 100%;
        max-width: 40em;
        padding: 15px;
        margin: auto;
    }

    .form-signup .checkbox {
        font-weight: 400;
    }

    .form-signup .form-floating:focus-within {
        z-index: 2;
    }

    .form-signup input[type="email"] {
        margin-bottom: -1px;
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 0;
    }

    .form-signup input[type="password"] {
        margin-bottom: 10px;
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }
</style>
