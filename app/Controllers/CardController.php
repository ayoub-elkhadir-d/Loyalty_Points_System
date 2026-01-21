<?php 
namespace App\Controllers;
use App\Models\productModel;
use App\Models\UserModel;
use App\Models\CardModel;
use App\Core\Controller;

//updatePoints
class CardController extends Controller{
  private $CardM;
function __construct(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/shopeasy-loyalty/public/login');
        }
        $this->CardM = new CardModel();
}
function getcardproducts(){
$products_card = $this->CardM->getproductsfromCard($_SESSION['user_id']);

$this->render('/products/cart.html', [
           'card_products' => $products_card
        ]);
}
function updateCard(){

$this->CardM->updateCard($_SESSION['user_id'],$_POST['product_id'],$_POST['quantity']);
$this->redirect('/shopeasy-loyalty/public/products/card');
}
function deleteitem(){

$this->CardM->RemoveCard($_SESSION['user_id'],$_POST['product_id']);
$this->redirect('/shopeasy-loyalty/public/products/card');
}
function additem(){
$this->CardM->addCard($_SESSION['user_id'],$_POST['product_id']);
$this->redirect('/shopeasy-loyalty/public/products/card');
}

}