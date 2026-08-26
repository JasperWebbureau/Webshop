<?php

namespace Flexgrid\Modules\Webshop\Repository;

use Flexgrid\Modules\Webshop\Entity\WebshopStockMutation;
use Repository\Repository;

class WebshopStockMutationRepository extends Repository
{
    public function getEntity()
    {
        return new WebshopStockMutation();
    }

    public function getByOrderId(int $orderId): array
    {
        return $this->select(true)
            ->where('order_id = ?', [$orderId])
            ->orderBy('id:DESC')
            ->get();
    }

    public function getByProductId(int $productId, int $limit = 50): array
    {
        return $this->select(true)
            ->where('product_id = ?', [$productId])
            ->orderBy('id:DESC')
            ->limit($limit)
            ->get();
    }
}
