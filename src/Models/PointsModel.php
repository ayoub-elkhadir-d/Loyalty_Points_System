<?php
namespace Models;

use Core\Database;

class PointsModel {
    private $db;
    private $pointsCalculator;
    
    public function __construct(Database $db, PointsCalculator $calculator) {
        $this->db = $db;
        $this->pointsCalculator = $calculator;
    }
    
    public function addPoints(int $userId, float $purchaseAmount, string $description): array {

        $points = $this->pointsCalculator->calculatePoints($purchaseAmount);
        
        return $this->db->transaction(function() use ($userId, $points, $description) {
           
            $this->db->query(
                "UPDATE users SET total_points = total_points + ? WHERE id = ?",
                [$points, $userId]
            );
            
       
            $newBalance = $this->getUserBalance($userId);
            
            $transactionId = $this->db->insert('points_transactions', [
                'user_id' => $userId,
                'type' => 'earned',
                'amount' => $points,
                'description' => $description,
                'balance_after' => $newBalance
            ]);
            
            return ['transaction_id' => $transactionId, 'points_added' => $points];
        });
    }
}