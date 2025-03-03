<?php
namespace App\Webshop\Repository;

use App\Webshop\Entity\WebshopOrder;
use Repository\Repository;
use App\Entity\_WebshopOrder;

class WebshopOrderRepository extends Repository {
    public function getEntity()
    {
        return new WebshopOrder();
    }

}