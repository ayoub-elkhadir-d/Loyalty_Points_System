<?php


namespace App\Controllers;

use App\Core\Controller;
use App\Models\UserModel;

class AuthController extends Controller {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new UserModel();
        
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    
    public function loginForm() {
        
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/dashboard');
        }
        
        $this->render('auth/login');
    }
    
    
    public function login() {
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }
        
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
       
        
    
        
       
            $user = $this->userModel->findByEmail($email);
             print_r($user);
            if ($user) {
                echo "hi";
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['total_points'] = $user['total_points'];
                
             
                
                $this->redirect('/shopeasy-loyalty/public/dashboard');
          
        }
        
        
        $this->render('auth/login', [
          
            'old_email' => $email
        ]);
    }
    
    
    public function logout() {
        session_destroy();
        $this->redirect('/shopeasy-loyalty/public/login');
    }
}