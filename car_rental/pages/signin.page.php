<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $CURRENT_USER == null && isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $whereClause = new WhereClause();
    $whereClause->where('email', $email);

    $signInUser = User::findOne($whereClause);
    if ($signInUser != null) {
        if (password_verify($password, $signInUser->getPassword())) {
            $_SESSION['user']['user_id'] = $signInUser->getUserId();
            $_SESSION['user']['user_type_id'] = $signInUser->getUserTypeId();

            header("Location: /");
            exit;
        } else {
            echo 'The provided password is incorrect.';
        }
    } else {
        echo 'No user was found matching this email address.';
    }
}

?>

<style>
    .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
    }

    @media (min-width: 768px) {
        .bd-placeholder-img-lg {
            font-size: 3.5rem;
        }
    }
</style>


<form method="POST">
    <img class="mb-4" src="https://getbootstrap.com/docs/5.0/assets/brand/bootstrap-logo.svg" alt="" width="72" height="57">
    <h1 class="h3 mb-3 fw-normal">Please sign in</h1>

    <div class="form-floating">
        <input type="email" class="form-control" id="floatingInput" name="email" placeholder="name@example.com">
        <label for="floatingInput">Email address</label>
    </div>
    <div class="form-floating">
        <input type="password" class="form-control" id="floatingPassword" name="password" placeholder="Password">
        <label for="floatingPassword">Password</label>
    </div>

    <button class="w-100 btn btn-lg btn-primary" type="submit">Sign in</button>
    <p class="mt-5 mb-3 text-muted">&copy; <?= date("Y"); ?>
    </p>
</form>
