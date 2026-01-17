<?php


namespace App\Core;

class Controller {
    protected function render($view, $data = []) {
        
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../Views');
        $twig = new \Twig\Environment($loader, [
            'cache' => false, 
        ]);
        
        
        $data['session'] = $_SESSION ?? [];
        
        echo $twig->render($view . '.twig', $data);
    }
    
    protected function redirect($url) {
        header('Location: ' . $url);
        exit;
    }
}