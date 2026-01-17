<?php

namespace App\Core;

class Router
{
    public $routes = [];

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
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        
        $action = $this->routes[$method][$uri] ?? null;
        
        if (!$action) {
            http_response_code(404);
            echo "404 - الصفحة غير موجودة<br>";
            echo "المسار المطلوب: $uri<br>";
            echo "";
            return;
        }

        [$controller, $methodName] = explode('@', $action);

        $controllerClass = "App\\Controllers\\$controller";
        
       
        if (!class_exists($controllerClass)) {
            http_response_code(500);
            echo "Controller '$controllerClass' غير موجود";
            return;
        }
        
        $controller = new $controllerClass();

        // تحقق من وجود الدالة
        if (!method_exists($controller, $methodName)) {
            http_response_code(500);
            echo "الدالة '$methodName' غير موجودة في '$controllerClass'";
            return;
        }

        $controller->$methodName();
    }
}