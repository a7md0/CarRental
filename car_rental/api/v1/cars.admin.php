<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../../include/env.php';
    require_once '../../include/auto_loader.php';

    require_once '../../include/authentication.php';

    /**
     * @param Car[] $car
     * @return string
     */
    function generateCarsTemplate($cars)
    {
        $cards = '';

        /** @var Car */
        foreach ($cars as $car) {
            $cards .= '<div class="card car-card" style="width: 18rem;">
            <img src="' . $car->getPreviewImage() . '" loading="lazy" class="card-img-top car-img" alt="' . $car->getCarModel()->getFullDisplayName() . ' Image">
            <div class="card-body text-center">
                <h5 class="card-title"><a href="javascript:void(0)" class="edit-button" data-car-id="' . $car->getCarId() . '">' . $car->getCarModel()->getFullDisplayName() . '</a></h5>
                <p class="card-subtitle"></p>
            </div>

            <ul class="list-group list-group-flush">
                <li class="list-group-item"><b>Car type</b>: ' . $car->getCarType()->getType() . '</li>
                <li class="list-group-item"><b>Number of seats</b>: ' . $car->getCarModel()->getNumberOfSeats() . '</li>
                <li class="list-group-item"><b>License plate:</b> ' . $car->getLicensePlate() . '</li>
                <li class="list-group-item"><b>Color:</b> ' . $car->getColor() . '</li>
                <li class="list-group-item"><b>Daily price:</b> BD' . $car->getDailyRentRate() . '</li>
            </ul>
            </div>';
        }

        return $cards;
    }

    $json = file_get_contents('php://input');
    $filters = json_decode($json);

    header('Content-Type: application/json');

    if ($CURRENT_USER === null || ($CURRENT_USER !== null && $CURRENT_USER->getUserType()->getAccessLevel() < 1)) {
        http_response_code($CURRENT_USER === null ? 401 : 403); // Response code to be either 401 or 403

        exit('{}');
    }

    $data = [
        'content' => '',
        'matching_results' => 0,
        'pages' => [
            'total' => 0,
            'current' => $filters->currentPage ?? 1,
        ]
    ];

    $carsLookup = new AdvancedCarsLookup();
    $carsLookup->setCurrentPage($filters->currentPage);

    if (isset($filters->filter_search) && strlen($filters->filter_search) >= 4) {
        $carsLookup->carModelWhereClause()->whereFullText(['brand', 'model'], $filters->filter_search);
    }

    if (isset($filters->filter_brands) && is_array($filters->filter_brands)) {
        $carsLookup->carModelWhereClause()->whereIn('brand', $filters->filter_brands);
    }

    if (isset($filters->filter_types) && is_array($filters->filter_types)) {
        $carsLookup->carModelWhereClause()->whereIn('car_type_id', $filters->filter_types);
    }

    if (isset($filters->filter_min_year) && isset($filters->filter_max_year)) {
        $carsLookup->carModelWhereClause()->whereBetween('year', $filters->filter_min_year, $filters->filter_max_year);
    }

    if (isset($filters->filter_min_seats) && isset($filters->filter_max_seats)) {
        $carsLookup->carModelWhereClause()->whereBetween('number_of_seats', $filters->filter_min_seats, $filters->filter_max_seats);
    }

    if (isset($filters->filter_min_price) && isset($filters->filter_max_price)) {
        $carsLookup->carWhereClause()->whereBetween('daily_rent_rate', $filters->filter_min_price, $filters->filter_max_price);
    }

    if (isset($filters->filter_colors) && is_array($filters->filter_colors)) {
        $carsLookup->carWhereClause()->whereIn('color', $filters->filter_colors);
    }

    $data['matching_results'] = $carsLookup->count();
    if ($data['matching_results'] > 0) {
        $cars = $carsLookup->find();
        $data['content'] = generateCarsTemplate($cars);
    }

    $data['pages']['total'] = $carsLookup->getTotalPages();
    // $data['query'] = $carsLookup->query;

    ob_clean(); // Clean (erase) the output buffer before the JSON [This would help eliminate the JSON parsing errors on the front-end if any warning were shown here]
    exit(json_encode($data));
}
