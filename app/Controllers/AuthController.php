<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\UserModel;

class AuthController extends Controller {
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new UserModel();
        session_start();
    }

    public function login() {
        if ($this->isLoggedIn()) {
            $this->redirect('/dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $user = $this->userModel->findByEmail($email);

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                
                $this->redirect('/dashboard');
            } else {
                $error = "Invalid email or password";
                $this->render('auth/login.twig', ['error' => $error]);
            }
        } else {
            $this->render('auth/login.twig');
        }
    }

    public function register() {
        if ($this->isLoggedIn()) {
            $this->redirect('/dashboard');
            }
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $email = $_POST['email'] ?? '';
                $password = $_POST['password'] ?? '';
                $name = $_POST['name'] ?? '';
                $confirmPassword = $_POST['confirm_password'] ?? '';
                
                
                $errors = [];
                
                if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Valid email is required";
            }
            
            if (strlen($password) < 6) {
                $errors[] = "Password must be at least 6 characters";
                }
                
                if ($password !== $confirmPassword) {
                    $errors[] = "Passwords do not match";
                    }
                    
                    if (empty($name)) {
                        $errors[] = "Name is required";
                        }
                        
                        
            $existingUser = $this->userModel->findByEmail($email);
            if ($existingUser) {
                $errors[] = "Email already registered";
                }
                
                if (empty($errors)) {
                    $success = $this->userModel->create($email, $password, $name);
                    
                    if ($success) {
                        $user = $this->userModel->findByEmail($email);
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_name'] = $user['name'];
                        $_SESSION['user_email'] = $user['email'];
                        
                        $this->redirect('/dashboard');
                        } else {
                            $errors[] = "Registration failed. Please try again.";
                            }
                            }
                            
                            $this->render('auth/register.twig', ['errors' => $errors]);
                            } else {
                                
                             }
        }
        
        public function logout() {
            session_destroy();
            $this->redirect('/login');
            }
            }

            
                // public function regester(){
                // }