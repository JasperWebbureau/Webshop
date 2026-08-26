<?php

namespace Flexgrid\Modules\Webshop\Repository;

use Flexgrid\Modules\Webshop\Entity\WebshopInvoice;
use Repository\Repository;

class WebshopInvoiceRepository extends Repository
{
    public function getEntity()
    {
        return new WebshopInvoice();
    }

    public function getRecent(int $limit = 100): array
    {
        return $this->select(true)
            ->orderBy('id:DESC')
            ->limit($limit)
            ->get();
    }

    public function findByOrderId(int $orderId)
    {
        $items = $this->select(true)
            ->where('order_id = ?', [$orderId])
            ->limit(1)
            ->get();

        return $items[0] ?? null;
    }
}
