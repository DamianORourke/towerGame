<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('TowerGameController');
$routes->setDefaultMethod('showNewGameForm'); // Set the default method to load the new game view
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

 $routes->get('/tower', 'TowerGameController::showNewGameForm');
 $routes->post('tower/new', 'TowerGameController::newGame');
 $routes->post( 'tower/restart', 'TowerGameController::restartGame' );
 $routes->post('tower/move/(:num)', 'TowerGameController::moveDisk/$1');
 

// API and other routes
$routes->group('api', function ($routes) {
    $routes->get('tower/(:num)', 'TowerGameController::getGameState/$1');
    $routes->post('tower/(:num)', 'TowerGameController::moveDisk/$1');
});

// Routes for new game view and game creation
// $routes->get('tower/new', 'TowerGameController::showNewGameForm');
// $routes->post('tower/new', 'TowerGameController::newGame');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 * There will often be times that you need additional routing and you
 * need to override or add to the default behavior. Environment-based
 * routes is one such time. require() additional route files here to
 * make that happen.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
