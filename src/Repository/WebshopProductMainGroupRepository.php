<?php

namespace Flexgrid\Modules\Webshop\Repository;

use Flexgrid\Modules\Webshop\Entity\WebshopProductMainGroup;
use Repository\Repository;

class WebshopProductMainGroupRepository extends Repository
{
    public function getEntity()
    {
        return new WebshopProductMainGroup();
    }

    public function getActive(): array
    {
        return $this->select(true)
            ->orderBy('title:ASC')
            ->get();
    }
}
