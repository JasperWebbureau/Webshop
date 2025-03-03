<?php
namespace App\Webshop\Repository;

use App\Webshop\Entity\WebshopOrderRow;
use Repository\Repository;
use App\Entity\_WebshopOrderRow;

class WebshopOrderRowRepository extends Repository {
    public function getEntity()
    {
        return new WebshopOrderRow();
    }

}