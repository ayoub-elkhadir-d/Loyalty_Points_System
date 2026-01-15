<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\PointsModel;
use App\Models\RewardModel;
use App\Models\PointsCalculator;

class DashboardController extends Controller {
    private $pointsModel;
    private $rewardModel;
    private $pointsCalculator;

    public function __construct() {
        parent::__construct();
        $this->requireLogin();
        
        $this->pointsModel = new PointsModel();
        $this->rewardModel = new RewardModel();
        $this->pointsCalculator = new PointsCalculator();
    }

    public function index() {
        $userId = $_SESSION['user_id'];
        
        $user = [
            'id' => $userId,
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email']
        ];
        
        $currentPoints = $this->pointsModel->getCurrentBalance($userId);
        $pointsSummary = $this->pointsModel->getPointsSummary($userId);
        $recentTransactions = $this->pointsModel->getTransactionHistory($userId, 10);
        $availableRewards = $this->rewardModel->findAll();
        
        $this->render('dashboard/index.twig', [
            'user' => $user,
            'currentPoints' => $currentPoints,
            'pointsSummary' => $pointsSummary,
            'recentTransactions' => $recentTransactions,
            'availableRewards' => $availableRewards
        ]);
    }
}