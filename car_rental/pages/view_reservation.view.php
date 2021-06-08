<?= $_GET['reservationCode'] ?>
<div class="container" style="max-width: 1080px;">
    <div class="py-5 text-center">
        <h2>View reservation</h2>
    </div>

    <?php if (isset($successMessage) && strlen($successMessage) > 0) { ?>
        <div class="alert alert-success" role="alert">
            <?= $successMessage ?>
        </div>
    <?php } ?>
    <div class="row g-5">
        <div class="col-md-12">
            <h4 class="mb-3">Lookup reservation</h4>
            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Reservation code" value="<?= isset($_GET['reservationCode']) ? $_GET['reservationCode'] : '' ?>" aria-describedby="view-button">
                        <button class="btn btn-secondary" type="button" id="view-button">View</button>
                    </div>
                </div>
                <div class="col-md-4"></div>
            </div>
            <h4 class="mb-3">Car details</h4>

            <div class="row">
                <div class="col-md-4">
                    <img src="<?= $carDetails->getPreviewImage(); ?>" class="card-img-top" alt="...">
                </div>

                <div class="col-md-1"></div>

                <div class="col-md-7">
                    <div class="row">
                        <div class="col-md-7">
                            <span class="fw-bold">Car</span><br />
                            <span><?= $carDetails->getCarModel()->getFullDisplayName() ?></span>
                        </div>
                        <div class="col-md-5">
                            <span class="fw-bold">Color</span><br />
                            <span><?= $carDetails->getColor() ?></span>
                        </div>
                        <div class="col-md-7">
                        <span class="fw-bold">Type</span><br />
                            <span><?= $carDetails->getCarType()->getType() ?></span>
                        </div>
                        <div class="col-md-5">
                            <span class="fw-bold">Daily rent rate</span><br />
                            <span>BD<?= $carDetails->getDailyRentRate() ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <form class="needs-validation" action="<?= $_SERVER['REQUEST_URI'] ?>" method="POST">
                <div class="row g-3">
                    <div class="col-12">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" name="address1" placeholder="1234 Main St" autocomplete="address-line1" required>
                        <div class="invalid-feedback">
                            Please enter your shipping address.
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="address2" class="form-label">Address 2 <span class="text-muted">(Optional)</span></label>
                        <input type="text" class="form-control" name="address2" placeholder="Apartment or suite" autocomplete="address-line2">
                    </div>

                    <div class="col-md-5">
                        <label for="country" class="form-label">Country</label>

                        <div class="invalid-feedback">
                            Please select a valid country.
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="city" class="form-label">City <span class="text-muted">(Optional)</span></label>
                        <input type="text" class="form-control" name="city" placeholder="" autocomplete="address-level2">
                        <div class="invalid-feedback">
                            City code required.
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="zip" class="form-label">Zip <span class="text-muted">(Optional)</span></label>
                        <input type="text" class="form-control" name="zip" placeholder="" autocomplete="postal-code">
                        <div class="invalid-feedback">
                            Zip code required.
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <h4 class="mb-3">Payment</h4>

                <div class="row gy-3">
                    <div class="col-md-6">
                        <label for="cc-number" class="form-label">Credit card number</label>
                        <input type="text" class="form-control" name="cc_number" placeholder="1234 5678 9112 1314" minlength="16" maxlength="16" autocomplete="cc-number" required>
                        <div class="invalid-feedback">
                            Credit card number is required
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="cc-expiration" class="form-label">Expiration</label>
                        <input type="text" class="form-control" name="cc_expiration" placeholder="MM/YY" pattern="(0[1-9]|1[0-2])\/?([0-9]{4}|[0-9]{2})" autocomplete="cc-exp" required>
                        <div class="invalid-feedback">
                            Expiration date required
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="cc-cvv" class="form-label">CVV</label>
                        <input type="text" class="form-control" name="cc_cvv" placeholder="000" minlength="3" maxlength="3" autocomplete="cc-csc" required>
                        <div class="invalid-feedback">
                            Security code required
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <button class="w-100 btn btn-primary btn-lg" type="submit">Checkout</button>
            </form>
        </div>
    </div>
</div>
