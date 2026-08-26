<?php

namespace Flexgrid\Modules\Webshop\Service;

use Flexgrid\Modules\Webshop\Entity\WebshopShippingMethod;
use Flexgrid\Modules\Webshop\Repository\WebshopShippingMethodRepository;

class ShippingService
{
    /** @var PriceService */
    protected $priceService;

    public function __construct(?PriceService $priceService = null)
    {
        $this->priceService = $priceService ?: new PriceService();
    }

    public function getAvailableMethods(float $subtotal): array
    {
        $methods = [];

        foreach ((new WebshopShippingMethodRepository())->getActive() as $method) {
            $methods[] = [
                'method' => $method,
                'price' => $this->getPrice($method, $subtotal),
                'price_formatted' => $this->priceService->format($this->getPrice($method, $subtotal)),
            ];
        }

        return $methods;
    }

    public function resolveMethod(int $methodId, float $subtotal): array
    {
        foreach ($this->getAvailableMethods($subtotal) as $item) {
            if ((int)$item['method']->getId() !== $methodId) {
                continue;
            }

            return [
                'success' => true,
                'method' => $item['method'],
                'price' => (float)$item['price'],
            ];
        }

        return [
            'success' => false,
            'message' => 'Kies een geldige verzendmethode.',
        ];
    }

    protected function getPrice(WebshopShippingMethod $method, float $subtotal): float
    {
        if ((float)$method->getFreeFrom() > 0 && $subtotal >= (float)$method->getFreeFrom()) {
            return 0.0;
        }

        return max(0.0, (float)$method->getPrice());
    }
}
