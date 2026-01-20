<?php
namespace App\Models;

use App\Core\Database;

class ProductModel{
    private $db;
    private $table = 'Products';

    public function __construct()
    {
      $this->db = Database::getConnection();  
      
    }


    function getallProducts(){
        $sql = "SELECT * FROM Products";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);

    }

    function getProductbyid($id){

      $sql = "SELECT * from Products where id = ?";

      $stmp = $this->db->prepare($sql);

      $stmp->execute([$id]);

      return $stmp->fetch(\PDO::FETCH_ASSOC);

    }



    }

    //
