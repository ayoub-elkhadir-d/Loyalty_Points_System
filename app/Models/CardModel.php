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
       public function getproductsfromCard($user_id) {
        $stmt = $this->db->prepare("SELECT p.* ,e.quntity as quntity FROM card e INNER JOIN users u ON e.userid = u.id INNER JOIN products p ON e.product_id = p.product_id where u.id =?;");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

     function updateCard($userid,$productId,$newQuentity){
        $stmt = $this->db->prepare("UPDATE Card set quntity = ? where userid = ? and product_id =?");
        $stmt->execute([$newQuentity, $userid,$productId]);

     }
     
}
// $obj = new CardModel();
//  print_r($obj->getproductsfromCard(1));