<?php

namespace Flexgrid\Modules\Webshop\Repository;

use Flexgrid\Modules\Webshop\Entity\WebshopOrderLine;
use Repository\Repository;

class WebshopOrderLineRepository extends Repository
{
    public function getEntity()
    {
        return new WebshopOrderLine();
    }

    public function getByOrderId(int $orderId): array
    {
        return $this->select(true)
            ->where('order_id = ?', [$orderId])
            ->get();
    }
}
