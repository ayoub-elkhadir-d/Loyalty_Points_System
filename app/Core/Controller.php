<?php
namespace App\Core;

class Controller {
    protected $twig;

    public function __construct() {
        $loader = new \Twig\Loader\FilesystemLoader('../app/Views');
        $this->twig = new \Twig\Environment($loader, [
            'cache' => '../cache',
            'debug' => true
        ]);
        
        // Ajout d'extensions Twig utiles
        $this->twig->addExtension(new \Twig\Extension\DebugExtension());
    }

    protected function render($view, $data = []) {
        echo $this->twig->render($view, $data);
    }

    protected function redirect($url, $statusCode = 303) {
        header('Location: ' . $url, true, $statusCode);
        exit();
    }

    protected function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    protected function requireLogin() {
        if (!$this->isLoggedIn()) {
            $this->redirect('/login');
        }
    }

    protected function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}