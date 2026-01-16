<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class User
{
    public function findByEmail(string $email): ?array
    {
        $pdo = Database::getInstance();

        $stmt = $pdo->prepare(
            "SELECT * FROM users WHERE email = :email LIMIT 1"
        );
        $stmt->execute(['email' => $email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }
}
