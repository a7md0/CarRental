<?php

/**
 * @var UserCarReservation $reservation
 * @var CarDetail $carDetails
 * @var CarAccessory[] $accessories
 * @var float $paidAmount
 * @var float $totalAmount
 * @var float $dueAmount
 * @var string $reservationCode
 */
?>
<div class="container" style="max-width: 1080px;">
    <div class="py-5 text-center">
        <h2>View reservation</h2>
    </div>

    <div class="row g-5">
        <div class="col-md-12">

            <?php if (isset($warningMessage) && strlen($warningMessage) > 0) { ?>
                <div class="alert alert-warning" role="alert">
                    <?= $warningMessage ?>
                </div>
            <?php } ?>
            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4 text-center">
                    <label for="reservation-code" class="form-label">Reservation code</label>
                    <div class="input-group mb-3">
                        <input type="text" id="reservation-code" name="reservation-code" class="form-control" placeholder="Reservation code" value="<?= isset($_GET['reservationCode']) ? $_GET['reservationCode'] : '' ?>" aria-describedby="view-button" autocomplete="on">
                        <button class="btn btn-secondary" type="button" id="view-button">Lookup</button>
                    </div>
                </div>
                <div class="col-md-4"></div>
            </div>

            <?php if (isset($successMessage) && strlen($successMessage) > 0) { ?>
                <div class="alert alert-success" role="alert">
                    <?= $successMessage ?>
                </div>
            <?php } ?>

            <?php if (isset($errorMessage) && strlen($errorMessage) > 0) { ?>
                <div class="alert alert-danger" role="alert">
                    <?= $errorMessage ?>
                </div>
            <?php } ?>

            <?php foreach ($infoMessages ?? [] as $infoMessage) { ?>
                <div class="alert alert-info" role="alert">
                    <?= $infoMessage ?>
                </div>
            <?php } ?>

            <?php if (isset($reservation)) { ?>
                <div class="card">
                    <article class="card-group-item">
                        <h5 class="card-header d-flex justify-content-between align-items-center">
                            View reservation (#<?= $reservationCode ?>)
                            <div>
                                <span class="tool-tip" <?php if (!$canAmend) {
                                                            echo 'data-bs-toggle="tooltip" data-bs-placement="bottom" title="Reservation cannot be amended (' . $cannotAmendMessage . ')"';
                                                        } ?>>
                                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#amendModal" <?php if (!$canAmend) {
                                                                                                                                                    echo ' disabled';
                                                                                                                                                } ?>>Amend</button>
                                </span>
                                <span class="tool-tip" <?php if (!$canCancel) {
                                                            echo 'data-bs-toggle="tooltip" data-bs-placement="bottom" title="Reservation cannot be canceled (' . $cannotCancelMessage . ')"';
                                                        } ?>>
                                    <button type="button" class="btn btn-sm btn-danger" id="cancel-button" <?php if (!$canCancel) {
                                                                                                                echo ' disabled';
                                                                                                            } ?>>Cancel</button>
                                </span>
                            </div>
                        </h5>


                        <div class="card-body m-3">
                            <h4 class="mb-3">Reservation details</h4>

                            <div class="row d-flex align-items-center text-center mb-4">
                                <div class="col-md-4 mb-2">
                                    <h6>Pickup date</h6>
                                    <span><?= $reservation->getPickupDate() ?></span>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <h6>Return date</h6>
                                    <span><?= $reservation->getReturnDate() ?></span>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <h6>Placed at</h6>
                                    <span><?= $reservation->getCreatedAt() ?></span>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <h6>Total amount</h6>
                                    <span>BD<?= $totalAmount ?></span>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <h6>Paid amount</h6>
                                    <span>BD<?= $paidAmount ?></span>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <h6>Status</h6>
                                    <span><?= $reservation->getStatus() ?></span>
                                </div>
                            </div>

                            <?php if (isset($carDetails)) { ?>
                                <h4 class="mb-3">Car details</h4>

                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <img src="<?= $carDetails->getPreviewImage(); ?>" class="card-img-top" alt="...">
                                    </div>

                                    <!-- <div class="col-md-1"></div> -->

                                    <div class="col-md-8">
                                        <div class="row align-items-center text-center mb-4">
                                            <div class="col-md-6 mb-2">
                                                <h6>Car</h6>
                                                <span><?= $carDetails->getCarModel()->getFullDisplayName() ?></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <h6>Color</h6>
                                                <span><?= $carDetails->getColor() ?></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <h6>Type</h6>
                                                <span><?= $carDetails->getCarType()->getType() ?></span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <h6>Daily rent rate</h6>
                                                <span>BD<?= $carDetails->getDailyRentRate() ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php } ?>

                            <?php if (isset($accessories)) { ?>
                                <div class="col-md-12">
                                    <!-- <h5 class="mb-3">Accessories</h5> -->
                                    <h4 class="d-flex justify-content-between align-items-center mb-3">
                                        <span>Accessories</span>
                                        <span class="badge bg-secondary rounded-pill"><?= count($accessories); ?></span>
                                    </h4>

                                    <ul class="list-group mb-3">
                                        <?php foreach ($accessories as $accessory) { ?>
                                            <li class="list-group-item d-flex justify-content-between lh-sm">
                                                <div class="p-4">
                                                    <img src="<?= $accessory->getPreviewImage(); ?>" class="card-img-top" style="object-fit: contain; max-width: 6em; max-height: 6em;">
                                                </div>
                                                <div class="p-2 flex-fill align-self-center">
                                                    <h6 class="my-0"><?= $accessory->getName(); ?></h6>
                                                    <!-- <small class="text-muted">Brief description</small> -->
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
                            <?php } ?>

                        </div>

                    </article>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="amendModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <form class="modal-content" action="<?= $_SERVER['REQUEST_URI'] ?>" method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Amend reservation dates</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label for="pickup_date" class="form-label h6">Pickup date</label>
                                        <input type="date" class="form-control" name="pickup_date" value="<?= $reservation->getPickupDate() ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label for="return_date" class="form-label h6">Return date</label>
                                        <input type="date" class="form-control" name="return_date" value="<?= $reservation->getReturnDate() ?>" required>
                                    </div>

                                    <div class="col-md-12 mt-2">
                                        <input class="form-check-input me-1" type="checkbox" name="amendReservation" value="true" required>
                                        I acknowledge that amending this reservation will incur a 10% fees, and I would not be able to amend this reservation again.
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Amend</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<script>
    const viewButton = document.querySelector("#view-button");
    viewButton.addEventListener('click', (event) => {
        const reservationCodeElement = document.querySelector("#reservation-code");

        submit_get({
            reservationCode: reservationCodeElement.value
        });
    });

    const cancelButton = document.querySelector("#cancel-button");
    if (cancelButton) {
        cancelButton.addEventListener('click', (event) => {
            const isConfirmed = window.confirm(`Are you sure about cancelling this reservation? This action cannot be undone.`);

            if (isConfirmed === true) {
                post(window.location, {
                        reservationCode: '<?= @$reservationCode ?>',
                        cancelReservation: true
                    },
                    'post');
            }
        });
    }
</script>
