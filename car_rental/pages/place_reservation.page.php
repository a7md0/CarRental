<div class="container" style="max-width: 1080px;">
    <div class="py-5 text-center">
        <h2>Place reservation</h2>
        <p class="lead">Below is an example form built entirely with Bootstrap’s form controls. Each required form group has a validation state that can be triggered by attempting to submit the form without completing it.</p>
    </div>

    <form class="row g-5">
        <div class="col-md-5 col-lg-4 order-md-last">
            <h4 class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-primary">Your cart</span>
                <span class="badge bg-primary rounded-pill">3</span>
            </h4>
            <ul class="list-group mb-3">
                <li class="list-group-item d-flex justify-content-between lh-sm">
                    <div>
                        <h6 class="my-0">Product name</h6>
                        <small class="text-muted">Brief description</small>
                    </div>
                    <span class="text-muted">$12</span>
                </li>
                <li class="list-group-item d-flex justify-content-between lh-sm">
                    <div>
                        <h6 class="my-0">Second product</h6>
                        <small class="text-muted">Brief description</small>
                    </div>
                    <span class="text-muted">$8</span>
                </li>
                <li class="list-group-item d-flex justify-content-between lh-sm">
                    <div>
                        <h6 class="my-0">Third item</h6>
                        <small class="text-muted">Brief description</small>
                    </div>
                    <span class="text-muted">$5</span>
                </li>
                <li class="list-group-item d-flex justify-content-between bg-light">
                    <div class="text-success">
                        <h6 class="my-0">Promo code</h6>
                        <small>EXAMPLECODE</small>
                    </div>
                    <span class="text-success">−$5</span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Total (USD)</span>
                    <strong>$20</strong>
                </li>
            </ul>

            <button class="w-100 btn btn-primary btn-lg" type="submit">Place reservation</button>
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
                                <img src="assets/images/cars/ford_explorer_2020_black.jpg" class="card-img-top" alt="...">
                            </div>

                            <div class="col-md-1"></div>

                            <div class="col-md-7">
                                <div class="row">
                                    <div class="col-md-7">
                                        <span class="fw-bold">Car</span><br />
                                        <span>Hyundai Accent (2020)</span>
                                    </div>
                                    <div class="col-md-5">
                                        <span class="fw-bold">Color</span><br />
                                        <span>Black</span>
                                    </div>
                                    <div class="col-md-7">
                                    <span class="fw-bold">Type</span><br />
                                        <span>Sedan</span>
                                    </div>
                                    <div class="col-md-5">
                                    <span class="fw-bold">Daily rent rate</span><br />
                                        <span>BD10.000</span>
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
                                <input type="date" class="form-control" id="pickupDate" placeholder="" value="" readonly>
                                <div class="invalid-feedback">
                                    Valid pickup date is required.
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <label for="returnDate" class="form-label">Return date</label>
                                <input type="date" class="form-control" id="returnDate" placeholder="" value="" readonly>
                                <div class="invalid-feedback">
                                    Valid return date is required.
                                </div>
                            </div>

                            <div class="col-md-12">
                                <h5 class="mb-3">Accessories</h5>

                                <ul class="list-group mb-3">
                                    <li class="list-group-item d-flex justify-content-between lh-sm">
                                        <div class="p-5">
                                            <img src="..." class="card-img-top" alt="...">
                                        </div>
                                        <div class="p-2 flex-fill align-self-center">
                                            <h6 class="my-0">Product name</h6>
                                            <small class="text-muted">Brief description</small>
                                        </div>
                                        <div class="p-2 align-self-center">
                                            <span class="text-muted">$12</span>
                                        </div>
                                    </li>
                                </ul>
                            </div>

                            <div class="col-md-12">
                                <div class="d-flex flex-row flex-nowrap overflow-auto">
                                    <?php for ($i = 0; $i < 5; $i++) {
                                    ?>
                                        <div class="card card-block mx-2" style="min-width: 14rem;">
                                            <img src="..." class="card-img-top" alt="...">
                                            <div class="card-body">
                                                <h5 class="card-title">Card title</h5>
                                                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                                                <a href="#" class="btn btn-primary">Go somewhere</a>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            </div>

        </div>
    </form>
</div>
