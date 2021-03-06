<div class="container" style="max-width: 1080px;">
    <div class="py-5 text-center">
        <h2>Place reservation</h2>
    </div>

    <div class="row g-5">
        <div class="col-md-5 col-lg-4 order-md-last">
            <h4 class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-primary">Your cart</span>
                <span class="badge bg-primary rounded-pill"><?= count($cartItems); ?></span>
            </h4>
            <ul class="list-group mb-3">
                <?php
                foreach ($cartItems as $item) {
                ?>
                    <li class="list-group-item d-flex justify-content-between lh-sm">
                        <div>
                            <span class="my-0 text-truncate"><?= $item[0]; ?></span>
                            <small class="text-muted"><?= $item[1]; ?></small>
                        </div>
                        <span class="text-muted">BD<?= $item[2]; ?></span>
                    </li>
                <?php
                }
                ?>

                <li class="list-group-item d-flex justify-content-between">
                    <h6 class="my-0 text-truncate">Total</h6>
                    <strong>BD<?= $cartTotal; ?></strong>
                </li>
            </ul>


            <div class="justify-content-center">
                <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="POST" style="margin-bottom: 1em;">
                    <input type="hidden" name="place_reservation" />
                    <button type="submit" class="w-100 btn btn-primary btn-lg" type="submit">Place reservation</button>
                </form>
                <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="POST" style="text-align: center;">
                    <input type="hidden" name="cancel" />
                    <button type="submit" class="w-50 text-center btn btn-danger btn-sm">Cancel and return</button>
                </form>
            </div>

        </div>

        <div class="col-md-7 col-lg-8">
            <div class="card">
                <article class="card-group-item">
                    <header class="card-header">
                        <h6 class="title">Car Details</h6>
                    </header>
                    <div class="filter-content">
                        <div class="card-body row g-3">
                            <div class="col-md-4">
                                <img src="<?= $car->getPreviewImage(); ?>" class="card-img-top" alt="...">
                            </div>

                            <div class="col-md-1"></div>

                            <div class="col-md-7">
                                <div class="row">
                                    <div class="col-md-7">
                                        <span class="fw-bold">Car</span><br />
                                        <span><?= $car->getCarModel()->getFullDisplayName(); ?></span>
                                    </div>
                                    <div class="col-md-5">
                                        <span class="fw-bold">Color</span><br />
                                        <span><?= $car->getColor(); ?></span>
                                    </div>
                                    <div class="col-md-7">
                                    </div>
                                    <div class="col-md-5">
                                        <span class="fw-bold">Daily rent rate</span><br />
                                        <span>BD<?= $car->getDailyRentRate(); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            </div>

            <br>

            <div class="card">
                <article class="card-group-item">
                    <header class="card-header">
                        <h6 class="title">Reservation Details</h6>
                    </header>
                    <div class="filter-content">
                        <div class="card-body row g-3">
                            <div class="col-sm-6">
                                <label for="pickupDate" class="form-label">Pickup date</label>
                                <input type="date" class="form-control" id="pickupDate" value="<?= $pickupDateStr; ?>" readonly>
                                <div class="invalid-feedback">
                                    Valid pickup date is required.
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <label for="returnDate" class="form-label">Return date</label>
                                <input type="date" class="form-control" id="returnDate" value="<?= $returnDateStr; ?>" readonly>
                                <div class="invalid-feedback">
                                    Valid return date is required.
                                </div>
                            </div>

                            <div class="col-md-12">
                                <!-- <h5 class="mb-3">Accessories</h5> -->
                                <h6 class="d-flex justify-content-between align-items-center mb-3">
                                    <span>Accessories</span>
                                    <span class="badge bg-secondary rounded-pill"><?= count($pickedAccessories); ?></span>
                                </h6>

                                <ul class="list-group mb-3">
                                    <?php
                                    /**
                                     * @var CarAccessory[] $pickedAccessories
                                     * @var CarAccessory $accessory
                                     */
                                    foreach ($pickedAccessories as $accessory) {
                                    ?>
                                        <li class="list-group-item d-flex justify-content-between lh-sm">
                                            <div class="p-4">
                                                <img src="<?= $accessory->getPreviewImage(); ?>" class="card-img-top" style="object-fit: contain; max-width: 6em; max-height: 6em;">
                                            </div>
                                            <div class="p-2 flex-fill align-self-center">
                                                <h6 class="my-0"><?= $accessory->getName(); ?></h6>
                                                <small class="link-danger" style="cursor: pointer" onclick="post(window.location.href, { deleteAccessory: <?= $accessory->getCarAccessoryId() ?> })">Delete</small>
                                            </div>
                                            <div class="p-2 align-self-center">
                                                <span class="text-muted">BD<?= $accessory->getCharge(); ?></span>
                                            </div>
                                        </li>
                                    <?php
                                    }
                                    ?>
                                </ul>
                            </div>

                            <div class="col-md-12">
                                <div class="d-flex flex-row flex-nowrap overflow-auto">
                                    <?php
                                    /**
                                     * @var CarAccessory[] $availableAccessories
                                     * @var CarAccessory $accessory
                                     */
                                    foreach ($availableAccessories as $accessory) {
                                    ?>
                                        <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="POST">
                                            <input type="hidden" name="accessory_id" value="<?= $accessory->getCarAccessoryId(); ?>" />
                                            <div class="card card-block mx-2 text-center" style="min-width: 14rem;">
                                                <img src="<?= $accessory->getPreviewImage(); ?>" class="card-img-top" style="object-fit: contain; max-width: 10em; max-height: 10em; margin: 0 auto;" />
                                                <div class="card-body">
                                                    <h5 class="card-title"><?= $accessory->getName(); ?></h5>
                                                    <p class="card-text">BD<?= $accessory->getCharge(); ?></p>
                                                    <button type="submit" value="pick_accessory" class="btn btn-primary">Pick accessory</button>
                                                </div>
                                            </div>
                                        </form>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            </div>

        </div>
    </div>
</div>
