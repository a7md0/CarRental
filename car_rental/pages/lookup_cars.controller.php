<?php

$availableColors = Car::uniqueValues('color'); // All current cars colors
$availableBrands = CarModel::uniqueValues('brand'); // All current cars brands
$availableTypes = CarType::find(); // All current cars types

list($minYear, $minSeats, $maxYear, $maxSeats) = CarModel::aggregateValues(['MIN' => ['year', 'number_of_seats'], 'MAX' => ['year', 'number_of_seats']]); // min and max values for ranges
list($minDailyRentRate, $maxDailyRentRate) = Car::aggregateValues(['MIN' => 'daily_rent_rate', 'MAX' => 'daily_rent_rate']); // min and max  values for price range

$VALUES += [
    'availableColors' => $availableColors,
    'availableBrands' => $availableBrands,
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
    (() => {
        window.lookup_api = 'api/v1/lookup-available-cars.php';
        window.lookup_cars_ranges =  {
            'year': {
                'min': <?= $minYear; ?>,
                'max': <?= $maxYear; ?>
            },
            'seats': {
                'min': <?= $minSeats; ?>,
                'max': <?= $maxSeats; ?>
            },
            'price': {
                'min': <?= $minDailyRentRate; ?>,
                'max': <?= $maxDailyRentRate; ?>
            },
        };
    })();
</script>
<script src="assets/js/lookup_cars.js"></script>
<style>
a.page-link {
    -webkit-touch-callout: none; /* iOS Safari */
    -webkit-user-select: none; /* Safari */
     -khtml-user-select: none; /* Konqueror HTML */
       -moz-user-select: none; /* Firefox */
        -ms-user-select: none; /* Internet Explorer/Edge */
            user-select: none; /* Non-prefixed version, currently
                                  supported by Chrome and Opera */
}

a.page-link:hover {
    cursor: pointer;
}
</style>
