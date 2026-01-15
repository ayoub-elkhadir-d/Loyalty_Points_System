<?php
class ConectiontDb{
 private PDO $pdo;

    public function __construct() {
        $host = "localhost";
        $dbname = "library_db";
        $user = "root";
        $pass = "";

        try {
            $this->pdo = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8",
                $user,
                $pass
            );

            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function getInstance(): PDO {
        return $this->pdo;
    }
}