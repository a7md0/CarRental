<?php

$ROUTES = [
    'home' => new AdminOnlyRoute('Home', 'home'),
    'cars' => new AdminOnlyRoute('Cars', 'cars'),
    'popular-reserved-cars' => new AdminOnlyRoute('Popular Reserved Cars', 'popular_reserved_cars'),
    'sales-revenue' => new AdminOnlyRoute('Sales Revenue', 'sales_revenue'),
    // 'sign-in' => new UnauthorizedOnlyRoute('Sign-in', 'sign_in', 'pages/admin'),
];
