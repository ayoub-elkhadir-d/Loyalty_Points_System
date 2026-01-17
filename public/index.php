<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;

session_start();

$router = new Router();

$router->get('/shopeasy-loyalty/public/login', 'AuthController@loginForm');
$router->get('/shopeasy-loyalty/public/dashboard', 'DashboardController@display');
$router->post('/shopeasy-loyalty/public/login', 'AuthController@login');
$router->get('/shopeasy-loyalty/public/logout', 'AuthController@logout');

$router->dispatch();