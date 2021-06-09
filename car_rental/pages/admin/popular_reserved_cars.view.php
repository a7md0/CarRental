<?php

/**
 * @var CarModel[] $carModels
 */
?>

<div class="container d-flex h-100">

    <div class="row justify-content-center align-self-center w-100" style="margin: 3rem 2.5vw 0 5vw;">
        <div class="py-1 text-center">
            <h2>Most popular reserved cars</h2>
        </div>

        <div class="col-md-8">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">Booked (#)</th>
                        <th scope="col">Brand</th>
                        <th scope="col">Model</th>
                        <th scope="col">Year</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($carModels as $carModel) { ?>
                        <tr>
                            <th scope="row"><?= $carModel[0] ?></th>
                            <td><?= $carModel[1]->getBrand() ?></td>
                            <td><?= $carModel[1]->getModel() ?></td>
                            <td><?= $carModel[1]->getYear() ?></td>
                        </tr>
                    <?php } ?>
                </tbody>

            </table>
        </div>

    </div>
</div>
