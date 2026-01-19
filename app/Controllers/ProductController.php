<?php 


class ProductController{


function __construct(){

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/shopeasy-loyalty/public/login');
        }

}
  function displayAll(){


  
  }


}