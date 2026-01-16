<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    public function loginForm()
    {
        $this->render('auth/login.twig');
    }

    public function login()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $this->render('auth/login.twig', [
                'error' => 'Email ou mot de passe incorrect'
            ]);
            return;
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];

        $this->redirect('/dashboard');
    }

    public function logout()
    {
        session_destroy();
        $this->redirect('/login');
    }
}
