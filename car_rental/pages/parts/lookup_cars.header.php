<?php
    $pickupDate = @$_GET['pickup_date'];
    $returnDate = @$_GET['return_date'];

    $availableColors = Car::uniqueValues('color');
    $availableBrands = CarModel::uniqueValues('brand');
    $availableModels = CarModel::uniqueValues('model');
    $availableTypes = CarType::find();

    list($minYear, $minSeats, $maxYear, $maxSeats) = CarModel::aggregateValues(['MIN' => ['year', 'number_of_seats'], 'MAX' => ['year', 'number_of_seats']]);
    list($minDailyRentRate, $maxDailyRentRate) = Car::aggregateValues(['MIN' => 'daily_rent_rate', 'MAX' => 'daily_rent_rate']);

    // var_dump($availableColors);
    // var_dump($availableBrands);
    // var_dump($availableModels);

    // echo $minYear . '-' . $maxYear . '<br />';
    // echo $minDailyRentRate . '-' . $maxDailyRentRate . '<br />';
    // echo $minSeats . '-' . $maxSeats . '<br />';

    // $carsLookup = new AdvanceCarsLookup($pickupDate, $returnDate);
    // $cars = $carsLookup->find();

    $VALUES += [
        'availableColors' => $availableColors,
        'availableBrands' => $availableBrands,
        'availableModels' => $availableModels,
        'availableTypes' => $availableTypes,
        'minYear' => $minYear,
        'minSeats' => $minSeats,
        'maxYear' => $maxYear,
        'maxSeats' => $maxSeats,
        'minDailyRentRate' => $minDailyRentRate,
        'maxDailyRentRate' => $maxDailyRentRate,
    ];
?>

<script>
    document.addEventListener('DOMContentLoaded', (event) => {


    }, false);
</script>
