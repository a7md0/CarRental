<?php

$ROUTES = [
    'home' => new Route('Home', 'home', 'pages/admin'),
    'sign-in' => new UnauthorizedOnlyRoute('Sign-in', 'sign_in', 'pages/admin'),
];
