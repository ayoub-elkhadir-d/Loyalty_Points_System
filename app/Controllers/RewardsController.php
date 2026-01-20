<?php


namespace App\Controllers;

use App\Core\Controller;
use App\Models\RewardModel;
use App\Models\PointsModel;

class RewardsController extends Controller {
    private $rewardModel;
    private $pointsModel;
    
    public function __construct() {
         
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/shopeasy-loyalty/public/login');
        }
        
        $this->rewardModel = new RewardModel();
        $this->pointsModel = new PointsModel();
    }
    
    
    public function index() {

        $rewards = $this->rewardModel->getAll(true);
        
        $this->render('rewards/catalog', [
            'rewards' => $rewards,
            'user_points' => $_SESSION['total_points'] ?? 0,
            'filter' => 'all'
        ]);


    }
    
    

    
    
    public function show($id) {
        $reward = $this->rewardModel->findById($id);
        
        if (!$reward) {
            $_SESSION['error'] = 'Reward not found';
            $this->redirect('/shopeasy-loyalty/public/rewards');
        }
        
        $this->render('rewards/show', [
            'reward' => $reward,
            'user_points' => $_SESSION['total_points'] ?? 0
        ]);
    }
    
  public function redeem($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/shopeasy-loyalty/public/rewards/show/' . $id);
        }
        
        $userId = $_SESSION['user_id'];
       
        $result = $this->rewardModel->redeem($id, $userId);
        
        if ($result['success']) {
            $_SESSION['total_points'] = $result['new_balance'];
            
            $_SESSION['success'] = sprintf(
                'Successfully redeemed "%s"! %s points deducted. New balance: %s points.',
                $result['reward']['name'],
                number_format($result['points_deducted']),
                number_format($result['new_balance'])
            );
            
            $this->redirect('/shopeasy-loyalty/public/dashboard');
        } else {
          
            $_SESSION['error'] = 'Failed to redeem reward: ' . $result['error'];
            $this->redirect('/shopeasy-loyalty/public/rewards/show/' . $id);
        }
    }




}