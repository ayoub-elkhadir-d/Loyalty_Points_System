<?php 
namespace App\Controllers;
use App\Models\productModel;
use App\Models\UserModel;
use App\Core\Controller;

//updatePoints
class CardController extends Controller{

function __construct(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/shopeasy-loyalty/public/login');
        }
}


}