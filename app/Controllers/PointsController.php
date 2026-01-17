<?php


namespace App\Controllers;

use App\Core\Controller;
use App\Models\PointsModel;

class PointsController extends Controller {
    private $pointsModel;
    
    public function __construct() {
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/shopeasy-loyalty/public/login');
        }
        
        $this->pointsModel = new PointsModel();
    }
    
    
    public function history() {
        $userId = $_SESSION['user_id'];
        $transactions = $this->pointsModel->getUserTransactions($userId);
        
        $this->render('points/history', [
            'transactions' => $transactions
        ]);
    }
    
    
    public function addFromPurchase() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/shopeasy-loyalty/public/dashboard');
        }
        
        $userId = $_SESSION['user_id'];
        $purchaseAmount = $_POST['amount'] ?? 0;
        
        if ($purchaseAmount > 0) {
            
            $pointsEarned = $this->pointsModel->calculatePoints($purchaseAmount);
            
            if ($pointsEarned > 0) {
                
                $description = "Purchase of $" . number_format($purchaseAmount, 2);
                $this->pointsModel->addTransaction(
                    $userId,
                    'earned',
                    $pointsEarned,
                    $description
                );
                
                
                $userModel = new \App\Models\UserModel();
                $user = $userModel->findById($userId);
                $_SESSION['total_points'] = $user['total_points'];
            }
        }
        
        $this->redirect('/shopeasy-loyalty/public/dashboard');
    }
}