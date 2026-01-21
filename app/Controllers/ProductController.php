<?php 
namespace App\Controllers;
use App\Models\productModel;
use App\Models\UserModel;
use App\Core\Controller;

//updatePoints
class ProductController extends Controller{
    private $productM;
    private $Umodel;


function __construct(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/shopeasy-loyalty/public/login');
        }
         
        $this->productM = new ProductModel();
        $this->Umodel = new UserModel();
}


function getallproducts(){
 $products = $this->productM->getallProducts();
 
         $this->render('/products/catalog', [
           'products' => $products
        ]);
   }



}