<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\PointsModel;
use App\Models\PointsCalculator;

class PointsController extends Controller {
    private $pointsModel;
    private $pointsCalculator;

    public function __construct() {
        parent::__construct();
        $this->requireLogin();
        
        $this->pointsModel = new PointsModel();
        $this->pointsCalculator = new PointsCalculator();
    }

    public function history() {
        $userId = $_SESSION['user_id'];
        $transactions = $this->pointsModel->getTransactionHistory($userId);
        
        $this->render('points/history.twig', [
            'transactions' => $transactions
        ]);
    }

    public function addFromPurchase($purchaseAmount) {
        $userId = $_SESSION['user_id'];
        $points = $this->pointsCalculator->calculatePointsFromPurchase($purchaseAmount);
        
        if ($points > 0) {
            $this->pointsModel->addTransaction(
                $userId,
                'earned',
                $points,
                "Purchase of $" . $purchaseAmount
            );
        }
        
        return $points;
    }

    public function apiBalance() {
        $this->requireLogin();
        
        $userId = $_SESSION['user_id'];
        $balance = $this->pointsModel->getCurrentBalance($userId);
        
        $this->jsonResponse([
            'balance' => $balance,
            'user_id' => $userId
        ]);
    }
}