<?php 
class ProductModel{
    private $db;
    private $table = 'Products';

    public function __construct()
    {
      $this->db = Database::getConnection();  
      
    }


    function getallProducts(){
        $sql = "SELECT * FROM $table";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
    }

    function buyProduct($id){


        
    }



    }
