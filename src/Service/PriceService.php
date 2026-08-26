<?php

namespace Flexgrid\Modules\Webshop\Service;

use Flexgrid\Modules\Webshop\Entity\WebshopProduct;

class PriceService
{
    public function getUnitPrice(WebshopProduct $product): float
    {
        $price = (float)$product->getPrice();
        $salePrice = (float)$product->getSalePrice();

        if ($salePrice > 0 && $salePrice < $price) {
            return $salePrice;
        }

        return $price;
    }

    public function getLineTotal(WebshopProduct $product, int $quantity): float
    {
        return $this->getUnitPrice($product) * max(1, $quantity);
    }

    public function format(float $amount): string
    {
        return '&euro; ' . number_format($amount, 2, ',', '.');
    }
}
