<?php
namespace App\Webshop\Repository;

use App\Webshop\Entity\WebshopProduct;
use Repository\Repository;

class WebshopProductRepository extends Repository {
    public function getEntity()
    {
        return new WebshopProduct();
    }

}