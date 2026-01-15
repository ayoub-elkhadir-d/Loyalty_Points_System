<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class RewardModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findAll($activeOnly = true) {
        $sql = "SELECT * FROM rewards";
        if ($activeOnly) {
            $sql .= " WHERE is_active = 1";
        }
        $sql .= " ORDER BY points_required ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM rewards WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function redeem($userId, $rewardId, $pointsSpent) {
        $this->db->beginTransaction();
        
        try {
            // Vérifier le stock
            $reward = $this->findById($rewardId);
            if ($reward['stock'] == 0) {
                throw new \Exception('Reward out of stock');
            }
            
            // Réduire le stock si limité
            if ($reward['stock'] > 0) {
                $stmt = $this->db->prepare(
                    "UPDATE rewards SET stock = stock - 1 WHERE id = :id AND stock > 0"
                );
                $stmt->execute([':id' => $rewardId]);
                
                if ($stmt->rowCount() == 0) {
                    throw new \Exception('Reward no longer available');
                }
            }
            
            // Enregistrer la rédemption
            $stmt = $this->db->prepare(
                "INSERT INTO redeemed_rewards (user_id, reward_id, points_spent) 
                 VALUES (:user_id, :reward_id, :points_spent)"
            );
            
            $stmt->execute([
                ':user_id' => $userId,
                ':reward_id' => $rewardId,
                ':points_spent' => $pointsSpent
            ]);
            
            $redemptionId = $this->db->lastInsertId();
            
            $this->db->commit();
            return $redemptionId;
            
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getUserRedemptions($userId) {
        $stmt = $this->db->prepare(
            "SELECT rr.*, r.name, r.description 
             FROM redeemed_rewards rr
             JOIN rewards r ON rr.reward_id = r.id
             WHERE rr.user_id = :user_id
             ORDER BY rr.created_at DESC"
        );
        
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}