<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\RewardModel;
use App\Models\PointsModel;
use App\Models\PointsCalculator;

class RewardsController extends Controller {
    private $rewardModel;
    private $pointsModel;
    private $pointsCalculator;

    public function __construct() {
        parent::__construct();
        $this->requireLogin();
        
        $this->rewardModel = new RewardModel();
        $this->pointsModel = new PointsModel();
        $this->pointsCalculator = new PointsCalculator();
    }

    public function catalog() {
        $rewards = $this->rewardModel->findAll();
        $currentPoints = $this->pointsModel->getCurrentBalance($_SESSION['user_id']);
        
        $this->render('rewards/catalog.twig', [
            'rewards' => $rewards,
            'currentPoints' => $currentPoints
        ]);
    }

    public function redeem($rewardId) {
        $userId = $_SESSION['user_id'];
        
        // Vérifier si l'utilisateur peut échanger cette récompense
        $canRedeem = $this->pointsCalculator->canRedeemReward(
            $userId,
            $rewardId,
            $this->pointsModel,
            $this->rewardModel
        );
        
        if (!$canRedeem['can_redeem']) {
            $_SESSION['error'] = "You don't have enough points for this reward.";
            $this->redirect('/rewards');
        }
        
        $reward = $this->rewardModel->findById($rewardId);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Échanger la récompense
                $redemptionId = $this->rewardModel->redeem(
                    $userId,
                    $rewardId,
                    $reward['points_required']
                );
                
                // Déduire les points
                $this->pointsModel->addTransaction(
                    $userId,
                    'redeemed',
                    -$reward['points_required'],
                    "Redeemed: " . $reward['name']
                );
                
                $_SESSION['success'] = "Reward redeemed successfully!";
                $this->redirect('/dashboard');
                
            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                $this->redirect('/rewards');
            }
        } else {
            $this->render('rewards/redeem.twig', [
                'reward' => $reward,
                'canRedeem' => $canRedeem
            ]);
        }
    }

    public function myRewards() {
        $userId = $_SESSION['user_id'];
        $redemptions = $this->rewardModel->getUserRedemptions($userId);
        
        $this->render('rewards/my-rewards.twig', [
            'redemptions' => $redemptions
        ]);
    }
}