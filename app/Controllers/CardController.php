<?php
namespace App\Controllers;
use App\Models\productModel;
use App\Models\UserModel;
use App\Models\CardModel;
use App\Core\Controller;

class CardController extends Controller
{
    private $CardM;
    private $UserM;

    function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION["user_id"])) {
            $this->redirect("/shopeasy-loyalty/public/login");
        }
        $this->CardM = new CardModel();
        $this->UserM = new UserModel();
    }
    function getcardproducts()
    {
        $products_card = $this->CardM->getproductsfromCard(
            $_SESSION["user_id"]
        );
        //  print_r($products_card);
        $this->render("/products/cart.html", [

            "card_products" => $products_card
           
        ]);
    }
    function updateCard()
    {
        $this->CardM->updateCard(
            $_SESSION["user_id"],
            $_POST["product_id"],
            $_POST["quantity"]
        );
        $this->redirect("/shopeasy-loyalty/public/products/card");
    }
    function deleteitem()
    {
        $this->CardM->RemoveCard($_SESSION["user_id"], $_POST["product_id"]);
        $this->redirect("/shopeasy-loyalty/public/products/card");
    }
    function additem()
    {
        $this->CardM->addCard($_SESSION["user_id"], $_POST["product_id"]);
        $this->redirect("/shopeasy-loyalty/public/products/card");
    
    }
    function checkout(){
        $_SESSION['total_points'] = $_SESSION['total_points']+$_POST["points"];
      $this->render("/products/checkout.html", [
            "total" => $_POST["total"],
            "points" => $_POST["points"],
            'old_points' => $_SESSION['total_points']-$_POST["points"],
            'new_points' => $_SESSION['total_points']  ,
           
        ]);
        // $_SESSION['username'] = "JohnDoe"
    }
    function processcheckout(){
        $this->UserM->updatePoints($_SESSION["user_id"],$_SESSION['total_points']); 
          $this->redirect('/shopeasy-loyalty/public/dashboard');
    }
}
