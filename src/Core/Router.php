<?php
namespace Core;

class Router {
    private $routes = [];
    private $container;
    
    public function __construct(Container $container) {
        $this->container = $container;
    }
    
    public function add($method, $uri, $controllerAction) {
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'handler' => $controllerAction
        ];
    }
    
    public function dispatch($requestUri, $requestMethod) {
        foreach ($this->routes as $route) {
            if ($this->matches($route, $requestUri, $requestMethod)) {
                return $this->executeHandler($route['handler']);
            }
        }
        return $this->container->get('ErrorController')->notFound();
    }
}