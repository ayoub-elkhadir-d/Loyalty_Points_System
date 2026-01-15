<?php

namespace Shop\Core;

class Router {
    protected $routes = [];

    public function add($method, $uri, $controller, $action) 
    {
        $this->routes[$method][$uri] = [
            'controller' => $controller,
            'action' => $action
        ];
    }



    public function getRoutes() {
        return $this->routes;
    }

    public function dispatch($requestUri, $requestMethod) {
  

        if (isset($this->routes[$requestMethod][$requestUri])) {
            
            $route = $this->routes[$requestMethod][$requestUri];
            $controllerName = $route['controller']; 
            $actionName = $route['action']; 

            $fullControllerName = "src\\Controlers\\" . $controllerName;

         
            
            if (class_exists($fullControllerName)) {
               
               call_user_func_array([new $fullControllerName(), $actionName],[]);
            } else {
                echo "Error: Class $fullControllerName ";
            }

        } else {
            echo "404 - Page Not  ";
        }
    }
}
