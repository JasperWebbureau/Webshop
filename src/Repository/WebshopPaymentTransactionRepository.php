<?php

namespace Flexgrid\Modules\Webshop\Repository;

use Flexgrid\Modules\Webshop\Entity\WebshopPaymentTransaction;
use Repository\Repository;

class WebshopPaymentTransactionRepository extends Repository
{
    public function getEntity()
    {
        return new WebshopPaymentTransaction();
    }

    public function getRecent(int $limit = 100): array
    {
        return $this->select(true)
            ->orderBy('id:DESC')
            ->limit($limit)
            ->get();
    }

    public function getByOrderId(int $orderId): array
    {
        return $this->select(true)
            ->where('order_id = ?', [$orderId])
            ->orderBy('id:DESC')
            ->get();
    }

    public function findByReference(string $reference)
    {
        $items = $this->select(true)
            ->where('transaction_reference = ? OR provider_reference = ?', [$reference, $reference])
            ->limit(1)
            ->get();

        return $items[0] ?? null;
    }
}
