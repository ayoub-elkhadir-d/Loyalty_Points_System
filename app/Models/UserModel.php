<?php


namespace App\Models;

use App\Core\Database;

class UserModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getConnection();
    }
    
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    

    public function updatePoints($userId, $points) {
        $stmt = $this->db->prepare("UPDATE users SET total_points = ? WHERE id = ?");
        return $stmt->execute([$points, $userId]);
    }
    public function getPoints($userId) {
        $stmt = $this->db->prepare("SELECT total_points FROM  users WHERE id = ?");
        return $stmt->execute([$userId]);
    }
}