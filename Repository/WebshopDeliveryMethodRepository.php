<?php
namespace App\Webshop\Repository;

use App\Webshop\Entity\WebshopDeliveryMethod;
use Repository\Repository;

class WebshopDeliveryMethodRepository extends Repository {
    public function getEntity()
    {
        return new WebshopDeliveryMethod();
    }

}