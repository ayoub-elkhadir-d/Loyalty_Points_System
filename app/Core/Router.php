<?php

namespace App\Core;

class Router {
    protected $routes = [];

    public function get($path, $handler) {
        $this->routes['GET'][$path] = $handler;
    }

    public function post($path, $handler) {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $basePath = '/shopeasy-loyalty/public';
        if (strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
        if (empty($uri)) $uri = '/';

        foreach ($this->routes[$method] as $route => $handler) {
       
            $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_]+)', $route);
            $pattern = "#^" . $pattern . "$#";

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); 

                if (is_callable($handler)) {
                    return call_user_func_array($handler, $matches);
                }

                if (is_string($handler)) {
                    list($controllerName, $methodName) = explode('@', $handler);
                    $controllerName = "App\\Controllers\\" . $controllerName;

                    if (class_exists($controllerName)) {
                        $controller = new $controllerName();
                        return call_user_func_array([$controller, $methodName], $matches);
                    }
                }
            }
        }

        http_response_code(404);
        echo "404 - Page Not Found at: " . htmlspecialchars($uri);
    }
}