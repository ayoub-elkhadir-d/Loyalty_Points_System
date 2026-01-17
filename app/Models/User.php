<?php

namespace App\Models;

use App\Core\Database;

class User
{
    private $db;
    private $table = 'users';

    public function __construct()
    {

        $this->db = Database::getInstance()->getPDO();
    }

    public function login($email,$pass)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email and password_hash=:pass");
            $stmt->execute(['email' => $email,'pass' => $pass]
            );
            return $stmt->fetch();
        } catch (\PDOException $e) {
            die("err" . $e->getMessage());
        }
    }

  
    public function findById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
            $stmt->execute(['id' => $id]);
            return $stmt->fetch();
        } catch (\PDOException $e) {
            die("err" . $e->getMessage());
        }
    }



    public function verifyPassword($inputPassword, $hashedPassword)
    {
        return password_verify($inputPassword, $hashedPassword);
    }


    public function all()
    {
        try {
            $stmt = $this->db->query("SELECT * FROM {$this->table}");
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            die("" . $e->getMessage());
        }
    }
}