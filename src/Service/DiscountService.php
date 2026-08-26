<?php

namespace Flexgrid\Modules\Webshop\Service;

use Flexgrid\Modules\Webshop\Entity\WebshopDiscountCode;
use Flexgrid\Modules\Webshop\Repository\WebshopDiscountCodeRepository;

class DiscountService
{
    public function resolveDiscount(string $code, float $subtotal): array
    {
        $code = strtoupper(trim($code));
        if ($code === '') {
            return [
                'success' => true,
                'discount' => null,
                'amount' => 0.0,
                'code' => '',
            ];
        }

        $discount = (new WebshopDiscountCodeRepository())->findByCode($code);
        if (!$this->isValid($discount, $subtotal)) {
            return [
                'success' => false,
                'message' => 'Kortingscode is niet geldig.',
            ];
        }

        return [
            'success' => true,
            'discount' => $discount,
            'amount' => $this->calculateAmount($discount, $subtotal),
            'code' => $discount->getCode(),
        ];
    }

    public function markUsed(?WebshopDiscountCode $discount): void
    {
        if (!$discount || (int)$discount->getId() <= 0) {
            return;
        }

        $discount->setUsedCount((int)$discount->getUsedCount() + 1);
        (new WebshopDiscountCodeRepository())->add($discount);
    }

    protected function isValid($discount, float $subtotal): bool
    {
        if (!$discount instanceof WebshopDiscountCode || (int)$discount->getId() <= 0) {
            return false;
        }

        if ((int)$discount->getIsActive() !== 1) {
            return false;
        }

        if ((float)$discount->getValue() <= 0) {
            return false;
        }

        if ((float)$discount->getMinimumSubtotal() > 0 && $subtotal < (float)$discount->getMinimumSubtotal()) {
            return false;
        }

        if ((int)$discount->getStartsAt() > 0 && time() < (int)$discount->getStartsAt()) {
            return false;
        }

        if ((int)$discount->getExpiresAt() > 0 && time() > (int)$discount->getExpiresAt()) {
            return false;
        }

        if ((int)$discount->getMaxUses() > 0 && (int)$discount->getUsedCount() >= (int)$discount->getMaxUses()) {
            return false;
        }

        return true;
    }

    protected function calculateAmount(WebshopDiscountCode $discount, float $subtotal): float
    {
        if ($discount->getDiscountType() === 'percentage') {
            return min($subtotal, $subtotal * ((float)$discount->getValue() / 100));
        }

        return min($subtotal, (float)$discount->getValue());
    }
}
