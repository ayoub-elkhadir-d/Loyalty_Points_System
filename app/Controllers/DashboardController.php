<?php


namespace App\Controllers;

use App\Core\Controller;
use App\Models\PointsModel;
use App\Models\UserModel;

class DashboardController extends Controller {
    private $pointsModel;
    private $userModel;
    
    public function __construct() {
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/shopeasy-loyalty/public/login');
        }
        
        $this->pointsModel = new PointsModel();
        $this->userModel = new UserModel();
    }
    
    
    public function display() {
        $userId = $_SESSION['user_id'];
        
        
        $user = $this->userModel->findById($userId);
        if ($user) {
            $_SESSION['total_points'] = $user['total_points'];
        }
        
        
        $recentTransactions = $this->pointsModel->getUserTransactions($userId);
        
        
        $stats = $this->pointsModel->getUserPointsStats($userId);
        
        $this->render('dashboard/index', [
            'user_name' => $_SESSION['user_name'] ?? 'User',
            'total_points' => $_SESSION['total_points'] ?? 0,
            'transactions' => $recentTransactions,
            'stats' => $stats
        ]);
    }
}