<?php
namespace App\Models;

class PointsCalculator {
    private $pointsPerDollar = 0.1; // 10 points par 100$ = 0.1 point par dollar
    
    public function calculatePointsFromPurchase($amount) {
        return floor($amount * $this->pointsPerDollar);
    }
    
    public function calculateRequiredPointsForReward($rewardId, $rewardModel) {
        $reward = $rewardModel->findById($rewardId);
        return $reward ? $reward['points_required'] : 0;
    }
    
    public function canRedeemReward($userId, $rewardId, $pointsModel, $rewardModel) {
        $currentPoints = $pointsModel->getCurrentBalance($userId);
        $requiredPoints = $this->calculateRequiredPointsForReward($rewardId, $rewardModel);
        
        return [
            'can_redeem' => $currentPoints >= $requiredPoints,
            'current_points' => $currentPoints,
            'required_points' => $requiredPoints,
            'missing_points' => max(0, $requiredPoints - $currentPoints)
        ];
    }
}