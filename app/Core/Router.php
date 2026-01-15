<?php
namespace App\Core;

class Router {
    private $routes = [];

    public function add($method, $path, $controllerAction) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'controllerAction' => $controllerAction
        ];
    }

    public function dispatch($requestUri, $requestMethod) {
        // Nettoyer l'URL
        $uri = parse_url($requestUri, PHP_URL_PATH);
        $uri = rtrim($uri, '/');
        if (empty($uri)) {
            $uri = '/';
        }

        $requestMethod = strtoupper($requestMethod);

        foreach ($this->routes as $route) {
            // Convertir les paramètres dynamiques
            $pattern = preg_replace('/\{(\w+)\}/', '([^/]+)', $route['path']);
            $pattern = "@^" . $pattern . "$@D";

            if ($route['method'] === $requestMethod && preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // Retirer le match complet

                // Séparer controller et action
                list($controller, $action) = explode('@', $route['controllerAction']);
                
                // Charger le contrôleur
                $controllerClass = "App\\Controllers\\{$controller}";
                if (class_exists($controllerClass)) {
                    $controllerInstance = new $controllerClass();
                    
                    // Appeler l'action avec les paramètres
                    call_user_func_array([$controllerInstance, $action], $matches);
                    return;
                }
            }
        }

        // Page 404
        http_response_code(404);
        echo "Page not found";
    }
}