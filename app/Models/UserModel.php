<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class UserModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($email, $password, $name) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $this->db->prepare(
            "INSERT INTO users (email, password_hash, name) 
             VALUES (:email, :password_hash, :name)"
        );
        
        return $stmt->execute([
            ':email' => $email,
            ':password_hash' => $hashedPassword,
            ':name' => $name
        ]);
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updatePoints($userId, $points) {
        $stmt = $this->db->prepare(
            "UPDATE users SET total_points = total_points + :points 
             WHERE id = :user_id"
        );
        
        return $stmt->execute([
            ':points' => $points,
            ':user_id' => $userId
        ]);
    }

    public function getTotalPoints($userId) {
        $stmt = $this->db->prepare(
            "SELECT total_points FROM users WHERE id = :user_id"
        );
        $stmt->execute([':user_id' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['total_points'] : 0;
    }
}