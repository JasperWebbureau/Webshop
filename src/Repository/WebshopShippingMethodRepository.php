<?php

namespace Flexgrid\Modules\Webshop\Repository;

use Flexgrid\Modules\Webshop\Entity\WebshopShippingMethod;
use Repository\Repository;

class WebshopShippingMethodRepository extends Repository
{
    public function getEntity()
    {
        return new WebshopShippingMethod();
    }

    public function getActive(): array
    {
        return $this->select(true)
            ->where('(is_active = ? OR ISNULL(is_active))', [1])
            ->orderBy('order:ASC')
            ->get();
    }
}
