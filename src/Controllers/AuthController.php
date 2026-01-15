<?php
namespace Controllers;

use Core\Controller;
use Models\UserModel;
use Services\SessionManager;

class AuthController extends Controller {
    private $userModel;
    private $session;
    
    public function __construct(UserModel $userModel, SessionManager $session) {
        $this->userModel = $userModel;
        $this->session = $session;
    }
    
    public function login(Request $request): Response {
        if ($request->isMethod('POST')) {
            $email = $request->post('email');
            $password = $request->post('password');
            
            $user = $this->userModel->authenticate($email, $password);
            
            if ($user) {
                $this->session->set('user_id', $user['id']);
                return $this->redirect('/dashboard');
            }
            
            return $this->render('auth/login.twig', [
                'error' => 'Identifiants incorrects'
            ]);
        }
        
        return $this->render('auth/login.twig');
    }
}