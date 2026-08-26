<?php

namespace Flexgrid\Modules\Webshop\Repository;

use Flexgrid\Modules\Webshop\Entity\WebshopDiscountCode;
use Repository\Repository;

class WebshopDiscountCodeRepository extends Repository
{
    public function getEntity()
    {
        return new WebshopDiscountCode();
    }

    public function findByCode(string $code)
    {
        $items = $this->select(true)
            ->where('code = ?', [strtoupper(trim($code))])
            ->limit(1)
            ->get();

        return $items[0] ?? null;
    }
}
