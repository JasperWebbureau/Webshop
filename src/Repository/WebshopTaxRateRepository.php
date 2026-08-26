<?php

namespace Flexgrid\Modules\Webshop\Repository;

use Flexgrid\Modules\Webshop\Entity\WebshopTaxRate;
use Repository\Repository;

class WebshopTaxRateRepository extends Repository
{
    public function getEntity()
    {
        return new WebshopTaxRate();
    }

    public function getActive(): array
    {
        return $this->select(true)
            ->where('(is_active = ? OR ISNULL(is_active))', [1])
            ->orderBy('rate:DESC')
            ->get();
    }

    public function getDefault()
    {
        $items = $this->select(true)
            ->where('(is_active = ? OR ISNULL(is_active)) AND is_default = ?', [1, 1])
            ->limit(1)
            ->get();

        return $items[0] ?? null;
    }
}
