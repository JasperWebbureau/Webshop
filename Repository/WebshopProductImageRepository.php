<?php
namespace App\Webshop\Repository;


use App\Webshop\Entity\WebshopProductImage;
use Repository\Repository;

class WebshopProductImageRepository extends Repository {
    public function getEntity()
    {
        return new WebshopProductImage();
    }

}