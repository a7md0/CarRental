<form method="POST" class="text-center">
    <h1 class="h3 mb-3 fw-normal">Please sign in</h1>

    <?php if (isset($_GET['errorMessage']) && strlen($_GET['errorMessage']) > 0) { ?>
        <div class="alert alert-danger" role="alert">
            <?= urldecode($_GET['errorMessage']) ?>
        </div>
    <?php } ?>
    <div class="form-floating">
        <input type="email" class="form-control" id="floatingInput" name="email" value="<?= @$_POST['email'] ?>" placeholder="name@example.com" autocomplete="email" required>
        <label for="floatingInput">Email address</label>
    </div>
    <div class="form-floating">
        <input type="password" class="form-control" id="floatingPassword" name="password" placeholder="Password" autocomplete="current-password" required>
        <label for="floatingPassword">Password</label>
    </div>

    <button class="w-100 btn btn-lg btn-primary" type="submit">Sign in</button>
    </p>
</form>
