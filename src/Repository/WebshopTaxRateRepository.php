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
            ->where('(is_hidden = ? OR is_hidden IS NULL)', [0])
            ->orderBy('rate:DESC')
            ->get();
    }

    public function getDefault()
    {
        $items = $this->select(true)
            ->where('(is_hidden = ? OR is_hidden IS NULL) AND is_default = ?', [0, 1])
            ->limit(1)
            ->get();

        return $items[0] ?? null;
    }
}
