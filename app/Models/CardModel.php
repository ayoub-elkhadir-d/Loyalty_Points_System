<?php


namespace App\Models;

use App\Core\Database;

class CardModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getConnection();
    }
    

       public function addCard($userId, $productid) {
        $stmt = $this->db->prepare("INSERT INTO Card(userid,product_id)
          VALUES (?,?);");
         $stmt->execute([$userId, $productid]);
    }
       public function RemoveCard($userId, $productid) {
        $stmt = $this->db->prepare("DELETE FROM Card where userid = ? and product_id =?");
        $stmt->execute([$userId, $productid]);
    }
       public function getproductsfromCard() {
        $stmt = $this->db->prepare("SELECT p.* FROM card e INNER JOIN users u ON e.userid = u.id INNER JOIN products p ON e.product_id = p.product_id where u.id =1;");
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

}