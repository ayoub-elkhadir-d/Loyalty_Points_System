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



}