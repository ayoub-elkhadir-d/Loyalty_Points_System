<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class PointsModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function addTransaction($userId, $type, $amount, $description = null, $purchaseId = null) {
        // Récupérer le solde actuel
        $currentBalance = $this->getCurrentBalance($userId);
        $newBalance = $currentBalance + $amount;
        
        $stmt = $this->db->prepare(
            "INSERT INTO points_transactions 
             (user_id, type, amount, description, balance_after, related_purchase_id) 
             VALUES (:user_id, :type, :amount, :description, :balance_after, :purchase_id)"
        );
        
        $result = $stmt->execute([
            ':user_id' => $userId,
            ':type' => $type,
            ':amount' => $amount,
            ':description' => $description,
            ':balance_after' => $newBalance,
            ':purchase_id' => $purchaseId
        ]);
        
        // Mettre à jour le total dans la table users
        if ($result) {
            $userModel = new UserModel();
            $userModel->updatePoints($userId, $amount);
        }
        
        return $result;
    }

    public function getCurrentBalance($userId) {
        $stmt = $this->db->prepare(
            "SELECT total_points FROM users WHERE id = :user_id"
        );
        $stmt->execute([':user_id' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['total_points'] : 0;
    }

    public function getTransactionHistory($userId, $limit = 50) {
        $stmt = $this->db->prepare(
            "SELECT * FROM points_transactions 
             WHERE user_id = :user_id 
             ORDER BY created_at DESC 
             LIMIT :limit"
        );
        
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPointsSummary($userId) {
        $stmt = $this->db->prepare(
            "SELECT 
                SUM(CASE WHEN type = 'earned' THEN amount ELSE 0 END) as total_earned,
                SUM(CASE WHEN type = 'redeemed' THEN amount ELSE 0 END) as total_redeemed,
                SUM(CASE WHEN type = 'expired' THEN amount ELSE 0 END) as total_expired
             FROM points_transactions 
             WHERE user_id = :user_id"
        );
        
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}