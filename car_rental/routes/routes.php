<?php

require_once('include/route.class.php');
require_once('routes.' . basename($_SERVER["SCRIPT_FILENAME"]));

$error401 = new ErrorRoute('Unauthorized', '401');
$error403 = new ErrorRoute('Forbidden', '403');
$error404 = new ErrorRoute('Not found', '404');

/**
 * @var Route
 */
$CURRENT_ROUTE = null;

$requestPage = isset($_GET['page']) ? $_GET['page'] : 'home';

foreach ($ROUTES as $page => $route) {
    if ($page == $requestPage) {
        /*if ($route instanceof AuthorizedOnlyRoute && notSignedIn) {
            $CURRENT_ROUTE = $error401;
            break;
        } else if ($route instanceof UnauthorizedOnlyRoute && signedIn) {
            $CURRENT_ROUTE = $error401;
            break;
        }*/

        $CURRENT_ROUTE = $route;
        break;
    }
}

if ($CURRENT_ROUTE == null) {
    $CURRENT_ROUTE = $error404;
}
