<?php

$ROUTES = [
    'home' => new AdminOnlyRoute('Home', 'home'),
    'cars' => new AdminOnlyRoute('Cars', 'cars'),
    'car-form' => new AdminOnlyRoute('Car Form', 'car_form'),
    'popular-reserved-cars' => new AdminOnlyRoute('Popular Reserved Cars', 'popular_reserved_cars'),
    'sales-revenue' => new AdminOnlyRoute('Sales Revenue', 'sales_revenue'),
    'car-model-form' => new AdminOnlyRoute('Car Model Form', 'car_model_form'),
];
