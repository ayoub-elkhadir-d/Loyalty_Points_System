<?php


require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;

$router = new Router();


$router->get('/login', 'AuthController@loginForm');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@registerForm');
$router->post('/register', 'AuthController@register');
$router->get('/logout', 'AuthController@logout');


$router->get('/dashboard', 'DashboardController@display');


$router->get('/points/history', 'PointsController@history');
$router->post('/points/add-from-purchase', 'PointsController@addFromPurchase');


$router->get('/rewards', 'RewardsController@index');
$router->get('/rewards/affordable', 'RewardsController@affordable');
$router->get('/rewards/show/{id}', 'RewardsController@show');

$router->post('/rewards/redeem/{id}', 'RewardsController@redeem');
 $router->get('/products', 'ProductController@getallproducts');


 $router->get('/products/card', 'CardController@getcardproducts');
 $router->post('/products/update-cart', 'CardController@updateCard');
 $router->post('/products/delete-from-cart', 'CardController@deleteitem');
 $router->post('/products/card-add-item', 'CardController@additem');


$router->get('/', function() {
    header('Location: /login');
    exit;
});

$router->dispatch();