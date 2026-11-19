<?php

namespace Flexgrid\Modules\Webshop\Integration\Administration;

use Flexgrid\Modules\AdminCore\ValueObject\Currency;
use Flexgrid\Modules\AdminCore\ValueObject\Money;
use Flexgrid\Modules\AdminCore\ValueObject\TaxRate;
use Flexgrid\Modules\Webshop\Entity\WebshopOrder;

/** Maps immutable gross order prices; never reads current product/customer data. */
final class OrderInvoiceMapper
{
    public function map(WebshopOrder $order, array $orderLines): array
    {
        $currency = new Currency((string)$order->getCurrency());
        $subtotal = $this->money($order->getSubtotal(), $currency)->getMinorUnits();
        $discount = $this->money($order->getDiscountTotal(), $currency)->getMinorUnits();
        if (!$orderLines || $discount < 0 || $discount > $subtotal) {
            throw new \DomainException('Orderregels of korting zijn ongeldig.');
        }
        $lines = [];
        $grossSum = $netSum = $taxSum = $seenGross = $allocated = 0;
        foreach (array_values($orderLines) as $index => $line) {
            $gross = $this->money($line->getLineTotal(), $currency)->getMinorUnits();
            $quantity = (int)$line->getQuantity();
            if ($gross < 0 || $quantity < 1 || $this->money($line->getUnitPrice(), $currency)->getMinorUnits() * $quantity !== $gross) {
                throw new \DomainException('Orderregel sluit niet aan op aantal en stuksprijs.');
            }
            $seenGross += $gross;
            // Cumulative allocation preserves every discount cent, including mixed VAT.
            $cumulative = $subtotal > 0 ? intdiv($discount * $seenGross, $subtotal) : 0;
            $lineDiscount = $cumulative - $allocated;
            $allocated = $cumulative;
            $mapped = $this->line((string)$line->getProductTitle(), $quantity, $gross - $lineDiscount, (float)$line->getTaxRate(), $currency, ($index + 1) * 10);
            $lines[] = $mapped['line'];
            $grossSum += $gross - $lineDiscount;
            $netSum += $mapped['net'];
            $taxSum += $mapped['tax'];
        }
        if ($seenGross !== $subtotal || $allocated !== $discount) {
            throw new \DomainException('Orderregels sluiten niet aan op subtotaal en korting.');
        }
        $shipping = $this->money($order->getShippingTotal(), $currency)->getMinorUnits();
        if ($shipping < 0) { throw new \DomainException('Verzendbedrag is ongeldig.'); }
        if ($shipping > 0) {
            $mapped = $this->line((string)$order->getShippingMethodTitle() ?: 'Verzending', 1, $shipping, (float)$order->getShippingTaxRate(), $currency, (count($lines) + 1) * 10);
            $lines[] = $mapped['line'];
            $grossSum += $shipping;
            $netSum += $mapped['net'];
            $taxSum += $mapped['tax'];
        }
        if ($grossSum !== $this->money($order->getGrandTotal(), $currency)->getMinorUnits()) {
            throw new \DomainException('Factuurtotaal wijkt af van het ordertotaal.');
        }
        return ['lines' => $lines, 'net' => $netSum, 'tax' => $taxSum, 'gross' => $grossSum];
    }

    private function line(string $title, int $quantity, int $gross, float $percentage, Currency $currency, int $position): array
    {
        $rate = TaxRate::fromPercentage(number_format($percentage, 2, '.', ''));
        $category = [0 => 'zero', 900 => 'reduced', 2100 => 'standard'][$rate->getBasisPoints()] ?? null;
        if ($category === null) { throw new \DomainException('Btw-tarief wordt niet ondersteund door de webshopkoppeling.'); }
        $grossMoney = new Money($gross, $currency);
        $net = $rate->calculateNet($grossMoney);
        return ['net' => $net->getMinorUnits(), 'tax' => $gross - $net->getMinorUnits(), 'line' => [
            'description' => $title, 'quantity' => (string)$quantity, 'unit' => 'stuk',
            'unit_price' => (new Money((int)round($net->getMinorUnits() / $quantity), $currency))->format('.', ''),
            'gross_total' => $grossMoney->format('.', ''),
            'tax_rate' => number_format($percentage, 2, '.', ''), 'tax_category' => $category, 'position' => $position,
        ]];
    }

    private function money($amount, Currency $currency): Money
    {
        return Money::fromDecimal(number_format((float)$amount, 2, '.', ''), $currency);
    }
}
