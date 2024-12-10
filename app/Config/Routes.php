<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/user', 'UserController::index');
$routes->post('/insert', 'UserController::insertData');
$routes->post('/validate-step', 'UserController::validateStep');
$routes->get('/users', 'UserController::displayData');
// $routes->get('/users', 'UserController::fetchUsers');
$routes->post('/delete-user/(:num)', 'UserController::deleteUser/$1');
// $routes->post('/update-user', 'UserController::updateUser');


// $routes->get('/get-user/(:num)', 'UserController::getUser/$1');
$routes->post('/update-user', 'UserController::updateUser');


$routes->get('/get-user/(:num)', 'UserController::getUser/$1');
$routes->get('/user/getPaginatedUsers', 'UserController::getPaginatedUsers');

$routes->get('/user/fetchPaginatedData', 'UserController::fetchPaginatedData');
// $routes->post('/user/delete-user/(:num)', 'UserController::deleteUser/$1');
