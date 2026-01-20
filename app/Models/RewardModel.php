<?php


namespace App\Models;

use App\Core\Database;

class RewardModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getConnection();
    }
    
    
    public function getAll($availableOnly = false) {
        $sql = "SELECT * FROM rewards";
        
        if ($availableOnly) {
            $sql .= " WHERE stock > 0 OR stock = -1";
        }
        
        $sql .= " ORDER BY points_required ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM rewards WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    
    public function getAffordableRewards($userPoints) {
        $stmt = $this->db->prepare("
            SELECT * FROM rewards 
            WHERE (stock > 0 OR stock = -1) 
            AND points_required <= ? 
            ORDER BY points_required ASC
        ");
        $stmt->execute([$userPoints]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    
    public function updateStock($rewardId, $newStock) {
        $stmt = $this->db->prepare("UPDATE rewards SET stock = ? WHERE id = ?");
        return $stmt->execute([$newStock, $rewardId]);
    }
    
    
    public function redeem($rewardId, $userId) {
        
        
        
        try {
            
            $stmt = $this->db->prepare("
                SELECT * FROM rewards 
                WHERE id = ? 
                FOR UPDATE
            ");
            $stmt->execute([$rewardId]);
            $reward = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$reward) {
                throw new \Exception("Reward not found");
            }
            
            
            if ($reward['stock'] == 0) {
                throw new \Exception("Reward is out of stock");
            }
            
            
            if ($reward['stock'] > 0) {
                $newStock = $reward['stock'] - 1;
                $updateStmt = $this->db->prepare("UPDATE rewards SET stock = ? WHERE id = ?");
                $updateStmt->execute([$newStock, $rewardId]);
                
                
                if ($updateStmt->rowCount() === 0) {
                    throw new \Exception("Failed to update reward stock");
                }
            }
            
            
            $userStmt = $this->db->prepare("
                SELECT total_points FROM users 
                WHERE id = ? 
                FOR UPDATE
            ");
            $userStmt->execute([$userId]);
            $user = $userStmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$user) {
                throw new \Exception("User not found");
            }
            
            $currentPoints = $user['total_points'];
            
            
            if ($currentPoints < $reward['points_required']) {
                throw new \Exception("Not enough points to redeem this reward");
            }
            
            
            $newBalance = $currentPoints - $reward['points_required'];
            
            
            $updateUserStmt = $this->db->prepare("UPDATE users SET total_points = ? WHERE id = ?");
            $updateUserStmt->execute([$newBalance, $userId]);
            
            if ($updateUserStmt->rowCount() === 0) {
                throw new \Exception("Failed to update user points");
            }
            
            
            $transactionStmt = $this->db->prepare("
                INSERT INTO points_transactions 
                (user_id, type, amount, description, balance_after) 
                VALUES (?, 'redeemed', ?, ?, ?)
            ");
            
            $description = "Redeemed reward: " . $reward['name'];
            $transactionStmt->execute([
                $userId,
                $reward['points_required'],
                $description,
                $newBalance
            ]);
            
            
           
            
            return [
                'success' => true,
                'reward' => $reward,
                'points_deducted' => $reward['points_required'],
                'new_balance' => $newBalance,
                'transaction_id' => $this->db->lastInsertId()
            ];
            
        } catch (\Exception $e) {
            
         
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    
    public function canRedeem($rewardId, $userId) {
        try {
            
            $reward = $this->findById($rewardId);
            
            if (!$reward) {
                return [
                    'can_redeem' => false,
                    'error' => 'Reward not found'
                ];
            }
            
            
            if ($reward['stock'] == 0) {
                return [
                    'can_redeem' => false,
                    'error' => 'Reward is out of stock'
                ];
            }
            
            
            $userStmt = $this->db->prepare("SELECT total_points FROM users WHERE id = ?");
            $userStmt->execute([$userId]);
            $user = $userStmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$user) {
                return [
                    'can_redeem' => false,
                    'error' => 'User not found'
                ];
            }
            
            
            if ($user['total_points'] < $reward['points_required']) {
                return [
                    'can_redeem' => false,
                    'error' => 'Not enough points',
                    'points_needed' => $reward['points_required'] - $user['total_points']
                ];
            }
            
            return [
                'can_redeem' => true,
                'reward' => $reward,
                'user_points' => $user['total_points'],
                'points_after' => $user['total_points'] - $reward['points_required']
            ];
            
        } catch (\Exception $e) {
            return [
                'can_redeem' => false,
                'error' => 'Error checking redemption eligibility'
            ];
        }
    }
    
    
    public function getUserRedemptions($userId, $limit = 10) {
        $stmt = $this->db->prepare("
            SELECT 
                pt.*,
                r.name as reward_name,
                r.points_required,
                pt.created_at as redeemed_at
            FROM points_transactions pt
            LEFT JOIN rewards r ON pt.description LIKE CONCAT('%', r.name, '%')
            WHERE pt.user_id = ? 
            AND pt.type = 'redeemed'
            ORDER BY pt.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    
    public function getUserRedemptionStats($userId) {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_redemptions,
                SUM(amount) as total_points_redeemed,
                MIN(created_at) as first_redemption,
                MAX(created_at) as last_redemption
            FROM points_transactions 
            WHERE user_id = ? 
            AND type = 'redeemed'
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}