<?php
namespace App\Webshop\Repository;


use App\Webshop\Entity\WebshopProductGroup;
use Repository\Repository;

class WebshopProductGroupRepository extends Repository {
    public function getEntity()
    {
        return new WebshopProductGroup();
    }

}