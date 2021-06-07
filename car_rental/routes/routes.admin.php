<?php

$ROUTES = [
    'home' => new AdminOnlyRoute('Home', 'home'),
    'cars' => new AdminOnlyRoute('Cars', 'cars'),
    // 'sign-in' => new UnauthorizedOnlyRoute('Sign-in', 'sign_in', 'pages/admin'),
];
