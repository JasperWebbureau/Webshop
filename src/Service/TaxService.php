<?php

namespace Flexgrid\Modules\Webshop\Service;

use Flexgrid\Modules\Webshop\Entity\WebshopProduct;
use Flexgrid\Modules\Webshop\Entity\WebshopShippingMethod;
use Flexgrid\Modules\Webshop\Entity\WebshopTaxRate;
use Flexgrid\Modules\Webshop\Repository\WebshopTaxRateRepository;

class TaxService
{
    /** @var WebshopTaxRateRepository */
    protected $taxRateRepository;

    public function __construct(?WebshopTaxRateRepository $taxRateRepository = null)
    {
        $this->taxRateRepository = $taxRateRepository ?: new WebshopTaxRateRepository();
    }

    public function getProductRate(WebshopProduct $product): float
    {
        if (method_exists($product, 'getTaxRateId') && (int)$product->getTaxRateId() > 0) {
            return $this->getRateById((int)$product->getTaxRateId(), (float)$product->getTaxRate());
        }

        return (float)$product->getTaxRate();
    }

    public function getShippingRate(WebshopShippingMethod $method): float
    {
        if (method_exists($method, 'getTaxRateId') && (int)$method->getTaxRateId() > 0) {
            return $this->getRateById((int)$method->getTaxRateId(), 0.0);
        }

        $default = $this->taxRateRepository->getDefault();
        return $default instanceof WebshopTaxRate ? (float)$default->getRate() : 0.0;
    }

    public function calculateIncludedTax(float $amount, float $taxRate): float
    {
        if ($amount <= 0 || $taxRate <= 0) {
            return 0.0;
        }

        return $amount - ($amount / (1 + ($taxRate / 100)));
    }

    protected function getRateById(int $taxRateId, float $fallback): float
    {
        $taxRate = $this->taxRateRepository->findById($taxRateId);
        if (!$taxRate instanceof WebshopTaxRate || (int)$taxRate->getId() <= 0 || (int)$taxRate->getIsActive() !== 1) {
            return $fallback;
        }

        return (float)$taxRate->getRate();
    }
}
