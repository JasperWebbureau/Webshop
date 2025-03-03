<?php
namespace App\Webshop\Repository;

use App\Webshop\Entity\WebshopClientAdress;
use Repository\Repository;
use App\Entity\_WebshopClientAdress;

class WebshopClientAdressRepository extends Repository {
    public function getEntity()
    {
        return new WebshopClientAdress();
    }

}