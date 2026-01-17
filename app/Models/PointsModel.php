<?php


namespace App\Models;

use App\Core\Database;

class PointsModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getConnection();
    }
    
    
    public function addTransaction($userId, $type, $amount, $description = '') {
        
        $userModel = new UserModel();
        $user = $userModel->findById($userId);
        $currentBalance = $user['total_points'];
        
        
        if ($type === 'earned') {
            $newBalance = $currentBalance + $amount;
        } elseif ($type === 'redeemed' || $type === 'expired') {
            $newBalance = $currentBalance - $amount;
        } else {
            $newBalance = $currentBalance;
        }
        
        
        $userModel->updatePoints($userId, $newBalance);
        
        
        $stmt = $this->db->prepare("
            INSERT INTO points_transactions 
            (user_id, type, amount, description, balance_after) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $userId,
            $type,
            $amount,
            $description,
            $newBalance
        ]);
    }
    
    
    public function getUserTransactions($userId, $limit = null) {
        $sql = "SELECT * FROM points_transactions 
                WHERE user_id = ? 
                ";
        
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    
    public function getUserPointsStats($userId) {
        $stmt = $this->db->prepare("
            SELECT 
                SUM(CASE WHEN type = 'earned' THEN amount ELSE 0 END) as total_earned,
                SUM(CASE WHEN type = 'redeemed' THEN amount ELSE 0 END) as total_redeemed,
                SUM(CASE WHEN type = 'expired' THEN amount ELSE 0 END) as total_expired
            FROM points_transactions 
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    
    public function calculatePoints($purchaseAmount) {
        
        return floor($purchaseAmount / 100) * 10;
    }
}