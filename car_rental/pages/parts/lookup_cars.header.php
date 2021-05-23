<?php
// $pickupDate = @$_GET['pickup_date'];
// $returnDate = @$_GET['return_date'];

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
    (() => {
        const ranges = {
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

        function checkRange(element) {
            const filterMinElm = document.querySelector(`[name=filter_min_${element}]`);
            const filterMaxElm = document.querySelector(`[name=filter_max_${element}]`);

            filterMinElm.min = ranges[element].min;
            filterMaxElm.max = ranges[element].max;

            if (Number(filterMinElm.value) > Number(filterMaxElm.value)) {
                if (Number(filterMinElm.value) > Number(filterMaxElm.max)) {
                    filterMinElm.value = filterMinElm.min;
                    filterMaxElm.value = filterMaxElm.max;
                } else {
                    filterMaxElm.value = filterMinElm.value;
                }
            }
        }

        function checkReservationDate() {
            const filterPickupDateElm = document.querySelector('[name=filter_pickup_date]');
            const filterReturnDateElm = document.querySelector('[name=filter_return_date]');

            if (filterPickupDateElm.valueAsDate === null) {
                filterPickupDateElm.valueAsDate = new Date();
            }
            if (filterReturnDateElm.valueAsDate === null) {
                filterReturnDateElm.valueAsDate = new Date();
            }

            filterPickupDateElm.min = new Date().toISOString().split("T")[0];
            filterReturnDateElm.min = filterPickupDateElm.min;

            if (filterPickupDateElm.valueAsDate > filterReturnDateElm.valueAsDate) {
                filterReturnDateElm.valueAsDate = filterPickupDateElm.valueAsDate;
            }
        }

        function fetchResults(filters) {
            console.log(filters);

            fetch('lookup-cars-api.php', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(filters)
            }).then(rawResponse => rawResponse.json())
            .then(response => {
                console.log(response);
                const resultsElement = document.querySelector('#results');
                resultsElement.innerHTML = response.content;
            });
        }

        function onFilterChange(event) {
            const filterFormElement = document.querySelector('[name=filter_form]');

            const filtersValid = filterFormElement.checkValidity();
            if (!filtersValid) {
                filterFormElement.reportValidity();
                return;
            }

            checkReservationDate();
            checkRange('price');
            checkRange('year');
            checkRange('seats');

            // filtersValid = filterFormElement.checkValidity();
            // if (!filtersValid) {
            //     filterFormElement.reportValidity();
            //     return;
            // }

            const filterElements = document.querySelectorAll('[data-trigger-filter=true]');
            const data = {};
            filterElements.forEach(element => {
                // data[element.name]
                const name = element.name;
                let value = element.value;

                if (element.nodeName === 'SELECT') {
                    value = [...element.options].filter((x) => x.selected).map((x) => x.value);
                }

                data[name] = value;
            });

            fetchResults(data);
        }

        document.addEventListener('DOMContentLoaded', (event) => {
            checkReservationDate();

            const filterElements = document.querySelectorAll('[data-trigger-filter=true]');
            filterElements.forEach(element => {
                element.addEventListener('change', onFilterChange);
            });

        }, false);
    })();
</script>
