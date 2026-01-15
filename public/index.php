<?php
require_once '../vendor/autoload.php';
require_once '../app/Core/Config.php';

session_start();

// Configuration Twig
$loader = new \Twig\Loader\FilesystemLoader('../app/Views');
$twig = new \Twig\Environment($loader, [
    'cache' => '../cache',
    'debug' => true
]);

// Ajouter la session aux variables globales Twig
$twig->addGlobal('session', $_SESSION);

// Routeur
use App\Core\Router;

$router = new Router();

// Routes d'authentification
$router->add('GET', '/', 'DashboardController@index');
$router->add('GET', '/login', 'AuthController@login');
$router->add('POST', '/login', 'AuthController@login');
$router->add('GET', '/register', 'AuthController@register');
$router->add('POST', '/register', 'AuthController@register');
$router->add('GET', '/logout', 'AuthController@logout');

// Routes tableau de bord
$router->add('GET', '/dashboard', 'DashboardController@index');

// Routes points
$router->add('GET', '/points/history', 'PointsController@history');
$router->add('GET', '/api/points/balance', 'PointsController@apiBalance');

// Routes récompenses
$router->add('GET', '/rewards', 'RewardsController@catalog');
$router->add('GET', '/rewards/my', 'RewardsController@myRewards');
$router->add('GET', '/rewards/redeem/{id}', 'RewardsController@redeem');
$router->add('POST', '/rewards/redeem/{id}', 'RewardsController@redeem');

// Dispatch la requête
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

$router->dispatch($requestUri, $requestMethod);