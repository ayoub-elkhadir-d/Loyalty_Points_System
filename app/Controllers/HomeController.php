<?php

namespace App\Controllers;

class HomeController
{
    public function index()
    {
        echo "مرحباً بك في الصفحة الرئيسية!";
        // أو يمكنك عرض view
        // require_once __DIR__ . '/../Views/home.php';
    }
}