<?php

namespace Flexgrid\Modules\Webshop\Service;

use Flexgrid\App\Settings\Settings;
use Flexgrid\Modules\Webshop\Entity\WebshopInvoice;
use Flexgrid\Modules\Webshop\Entity\WebshopInvoiceLine;
use Flexgrid\Modules\Webshop\Entity\WebshopOrder;
use Flexgrid\Modules\Webshop\Repository\WebshopInvoiceLineRepository;
use Flexgrid\Modules\Webshop\Repository\WebshopInvoiceRepository;
use Flexgrid\Modules\Webshop\Repository\WebshopOrderLineRepository;
use Flexgrid\Modules\Webshop\Repository\WebshopOrderRepository;
use Flexgrid\Modules\Webshop\Repository\WebshopPaymentTransactionRepository;

class InvoiceService
{
    public function createForOrder(int $orderId): array
    {
        $order = (new WebshopOrderRepository())->findById($orderId);
        if (!$order || (int)$order->getId() <= 0) {
            return ['success' => false, 'message' => 'Order niet gevonden.'];
        }

        if ($this->hasOnlinePayment($order) && (string)$order->getPaymentStatus() !== 'paid') {
            return ['success' => false, 'message' => 'Voor online betalingen kan pas na een succesvolle betaling een factuur worden aangemaakt.'];
        }

        $invoiceRepository = new WebshopInvoiceRepository();
        $existing = $invoiceRepository->findByOrderId((int)$order->getId());
        if ($existing && (int)$existing->getId() > 0) {
            return [
                'success' => true,
                'message' => 'Er bestaat al een factuur voor deze order.',
                'invoice' => $existing,
            ];
        }

        $lines = (new WebshopOrderLineRepository())->getByOrderId((int)$order->getId());
        if (empty($lines)) {
            return ['success' => false, 'message' => 'Order heeft geen regels.'];
        }

        $invoice = new WebshopInvoice();
        $invoice
            ->setInvoiceNumber($this->generateInvoiceNumber())
            ->setOrderId((int)$order->getId())
            ->setInvoiceDate(time())
            ->setCustomerName((string)$order->getCustomerName())
            ->setCustomerEmail((string)$order->getCustomerEmail())
            ->setCustomerSnapshot((string)$order->getCustomerSnapshot())
            ->setStatus('issued')
            ->setCurrency((string)$order->getCurrency())
            ->setSubtotal((float)$order->getSubtotal())
            ->setTaxTotal((float)$order->getTaxTotal())
            ->setGrandTotal((float)$order->getGrandTotal());

        $invoice = $invoiceRepository->add($invoice);
        $this->copyLines($invoice, $order, $lines);
        if ($this->shouldAutoGeneratePdf()) {
            (new InvoicePdfService())->generate($invoice);
        }

        return [
            'success' => true,
            'message' => 'Factuur aangemaakt.',
            'invoice' => $invoice,
        ];
    }

    protected function copyLines(WebshopInvoice $invoice, WebshopOrder $order, array $orderLines): void
    {
        $repository = new WebshopInvoiceLineRepository();

        foreach ($orderLines as $orderLine) {
            $line = new WebshopInvoiceLine();
            $title = (string)$orderLine->getProductTitle();

            $line
                ->setInvoiceId((int)$invoice->getId())
                ->setTitle($title)
                ->setSku((string)$orderLine->getSku())
                ->setQuantity((int)$orderLine->getQuantity())
                ->setUnitPrice((float)$orderLine->getUnitPrice())
                ->setTaxRate((float)$orderLine->getTaxRate())
                ->setTaxTotal((float)$orderLine->getTaxTotal())
                ->setLineTotal((float)$orderLine->getLineTotal());

            $repository->add($line);
        }

        if ((float)$order->getShippingTotal() > 0) {
            $line = new WebshopInvoiceLine();
            $line
                ->setInvoiceId((int)$invoice->getId())
                ->setTitle((string)($order->getShippingMethodTitle() ?: 'Verzending'))
                ->setSku('')
                ->setQuantity(1)
                ->setUnitPrice((float)$order->getShippingTotal())
                ->setTaxRate((float)$order->getShippingTaxRate())
                ->setTaxTotal((float)$order->getShippingTaxTotal())
                ->setLineTotal((float)$order->getShippingTotal());

            $repository->add($line);
        }

        if ((float)$order->getDiscountTotal() > 0) {
            $line = new WebshopInvoiceLine();
            $line
                ->setInvoiceId((int)$invoice->getId())
                ->setTitle('Korting' . ($order->getDiscountCode() !== '' ? ' (' . $order->getDiscountCode() . ')' : ''))
                ->setSku('')
                ->setQuantity(1)
                ->setUnitPrice(0 - (float)$order->getDiscountTotal())
                ->setTaxRate(0)
                ->setTaxTotal(0)
                ->setLineTotal(0 - (float)$order->getDiscountTotal());

            $repository->add($line);
        }
    }

    protected function generateInvoiceNumber(): string
    {
        new Settings();
        $setting = Settings::get('webshop_invoice_prefix', [
            'value' => 'F',
            'label' => 'Factuurprefix',
        ]);
        $prefix = trim((string)($setting['value'] ?? 'F')) ?: 'F';

        return $prefix . date('Ymd') . '-' . strtoupper(substr(uniqid('', false), -6));
    }

    protected function shouldAutoGeneratePdf(): bool
    {
        new Settings();
        $setting = Settings::get('webshop_invoice_pdf_auto_generate', [
            'value' => 1,
            'label' => 'PDF automatisch genereren',
        ]);

        return (int)($setting['value'] ?? 1) === 1;
    }

    protected function hasOnlinePayment(WebshopOrder $order): bool
    {
        foreach ((new WebshopPaymentTransactionRepository())->getByOrderId((int)$order->getId()) as $transaction) {
            if ((string)$transaction->getProvider() !== 'manual') {
                return true;
            }
        }

        return false;
    }
}
