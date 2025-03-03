<?php
namespace App\Webshop\Webshop;

class Webshop{

    public function __construct()
    {
    }


    public function placeOrder()
    {
        $orders = new Orders();
        return $orders->placeOrder();;
    }
}