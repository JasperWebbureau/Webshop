<?php

namespace Flexgrid\Modules\Webshop\Service;

use Flexgrid\Modules\Webshop\Entity\WebshopProduct;

class PriceService
{
    public function getCardPricing(WebshopProduct $product): array
    {
        $variants = $product->getVariantClusterProducts(true);
        $variants = $variants ?: [$product];
        $prices = array_map([$this, 'getUnitPrice'], $variants);
        $minimum = min($prices);
        $maximum = max($prices);

        return [
            'price' => $minimum,
            'original' => count($variants) === 1 ? (float)$product->getPrice() : $minimum,
            'from' => $minimum !== $maximum,
            'sale' => count($variants) === 1 && $minimum < (float)$product->getPrice(),
        ];
    }

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
