<?php

/**
 * @var float totalAmount
 * @var float paidAmount
 * @var float owedAmount
 *
 * @var string from
 * @var string to
 */
?>

<div class="container d-flex h-100">

    <div class="row justify-content-center align-self-center w-100" style="margin: 3rem 2.5vw 0 5vw;">
        <div class="py-1 text-center">
            <h2>Sales revenue</h2>
        </div>

        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6 text-center">
                <label for="reservation-code" class="form-label">For period between</label>

                <form class="input-group mb-3" action="<?= $_SERVER['REQUEST_URI'] ?>" method="get">
                    <input type="hidden" name="p" value="<?= $_GET['p'] ?>">
                    <input type="date" class="form-control" name="from" value="<?= $from ?>" required>
                    <span class="input-group-text">-</span>
                    <input type="date" class="form-control" name="to" value="<?= $to ?>" required>
                    <button class="btn btn-secondary" type="submit" id="view-button">Lookup</button>
                </form>
            </div>
            <div class="col-md-3"></div>
        </div>

        <div class="col-md-8">
            <table class="table table-striped table-hover text-center">
                <thead>
                    <tr>
                        <th scope="col">Revenue</th>
                        <th scope="col">Paid</th>
                        <th scope="col">Owed</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>BD<?= $totalAmount ?></td>
                        <td>BD<?= $paidAmount ?></td>
                        <td>BD<?= $owedAmount ?></td>
                    </tr>
                </tbody>

            </table>
        </div>

    </div>
</div>
