<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, string $action)
    {
        $this->routes['GET'][$path] = $action;
    }

    public function post(string $path, string $action)
    {
        $this->routes['POST'][$path] = $action;
    }

    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = str_replace('/shopeasy-loyalty/public', '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

//$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $action = $this->routes[$method][$uri] ?? null;

        if (!$action) {
            http_response_code(404);
            echo "404 Not Found";
            return;
        }

        [$controller, $methodName] = explode('@', $action);

        $controllerClass = "App\\Controllers\\$controller";
        $controller = new $controllerClass();

        $controller->$methodName();
    }
}
