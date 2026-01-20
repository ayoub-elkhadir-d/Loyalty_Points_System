<?php

namespace App\Models;

use App\Core\Database;

class PointsTransaction
{
    private $db;
    private $table = 'points_transactions';

    public function __construct()
    {
        $this->db = Database::getInstance()->getPDO();
    }

    
    public function create($data)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO {$this->table} 
                (user_id, type, amount, description, balance_after) 
                VALUES (:user_id, :type, :amount, :description, :balance_after)
            ");
            
            return $stmt->execute([
                'user_id' => $data['user_id'],
                'type' => $data['type'],
                'amount' => $data['amount'],
                'description' => $data['description'],
                'balance_after' => $data['balance_after']
            ]);
        } catch (\PDOException $e) {
            error_log("Error creating transaction: " . $e->getMessage());
            return false;
        }
    }

   
    public function getUserTransactions($userId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM {$this->table} 
                WHERE user_id = :user_id
                ORDER BY created_at DESC
            ");
            
            $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
            
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Error fetching transactions: " . $e->getMessage());
            return [];
        }
    }

  
    public function calculatePointsFromPurchase($purchaseAmount)
    {
      
        return floor($purchaseAmount / 100) * 10;
    }

    
    public function addPurchasePoints($userId, $purchaseAmount, $description = "Purchase points")
    {
        $pointsEarned = $this->calculatePointsFromPurchase($purchaseAmount);
        
        if ($pointsEarned > 0) {
            
            $userModel = new User();
            $currentPoints = $userModel->getUserPoints($userId);
            $newBalance = $currentPoints + $pointsEarned;
            
            
            $userModel->updatePoints($userId, $newBalance);
            
            
            $this->create([
                'user_id' => $userId,
                'type' => 'earned',
                'amount' => $pointsEarned,
                'description' => $description,
                'balance_after' => $newBalance
            ]);
            
            return $pointsEarned;
        }
        
        return 0;
    }
}