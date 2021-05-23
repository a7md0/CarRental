<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once 'include/env.php';
    require_once 'include/auto_loader.php';

    /**
     * @param Car[] $car
     * @return string
     */
    function generateCarsTemplate($cars)
    {
        $cards = '';

        foreach ($cars as $car) {
            $cards .= '<div class="card car-card" style="width: 18rem;">
            <img src="' . $car->getPreviewImage() . '" loading="lazy" class="card-img-top" alt="...">
            <div class="card-body text-center">
                <h5 class="card-title">'.$car->getCarModel()->getFullDisplayName() . '</h5>
                <p class="card-subtitle">BD'. $car->getDailyRentRate() . ' | '. $car->getColor() . ' </p>
                <a href="#" class="btn btn-primary">Rent</a>
            </div>
            </div>';
        }

        return $cards;
    }

    $json = file_get_contents('php://input');
    $filters = json_decode($json);

    header('Content-Type: application/json');

    $data = [
        'content' => '<b></b>',
        'matching_results' => 0,
        'pages' => [],
        'query' => $filters->filter_pickup_date
    ];

    $carsLookup = new AdvanceCarsLookup($filters->filter_pickup_date, $filters->filter_return_date);

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

    $data['matching_results'] = $carsLookup->count();
    if ($data['matching_results'] > 0) {
        $cars = $carsLookup->find();
        $data['content'] = generateCarsTemplate($cars);
    }

    $data['query'] = $carsLookup->query;

    exit(json_encode($data));
}
