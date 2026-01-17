<?php
// app/Core/Database.php

namespace App\Core;

class Database {
    private static $connection = null;
    
    public static function getConnection() {
        if (self::$connection === null) {
            try {
                self::$connection = new \PDO(
                    'mysql:host=localhost;dbname=loyalty_points_system;charset=utf8',
                    'root',
                    '',
                    [
                        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
                    ]
                );
            } catch (\PDOException $e) {
                die('Database connection failed: ' . $e->getMessage());
            }
        }
        
        return self::$connection;
    }
}