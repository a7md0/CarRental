<?php

$ROUTES = [
    'home' => new Route('Home', 'home'),
    'sign-in' => new UnauthorizedOnlyRoute('Sign-in', 'signin'),
    'signup' => new UnauthorizedOnlyRoute('Signup', 'signup'),
    'lookup-cars' => new Route('Lookup cars', 'lookup_cars'),
    'place-reservation' => new AuthorizedOnlyRoute('Place reservation', 'place_reservation'),
    'confirm-reservation' => new AuthorizedOnlyRoute('Confirm reservation', 'confirm_reservation'),
    'reservations' => new AuthorizedOnlyRoute('Reservations', 'reservations'),
    'view-reservation' => new AuthorizedOnlyRoute('View reservation', 'view_reservation'),
    'amend-reservation' => new AuthorizedOnlyRoute('Amend reservation', 'amend_reservation'),
];
