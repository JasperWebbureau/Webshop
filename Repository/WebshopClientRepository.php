<?php
namespace App\Webshop\Repository;

use Repository\Repository;
use App\Webshop\Entity\WebshopClient;

class WebshopClientRepository extends Repository {
    public function getEntity()
    {
        return new WebshopClient();
    }

}