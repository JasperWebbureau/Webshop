<?php

namespace Flexgrid\Modules\Webshop\Service;

use Flexgrid\Modules\Webshop\Entity\WebshopOrder;
use Flexgrid\Modules\Webshop\Repository\WebshopInvoiceRepository;
use Flexgrid\Modules\Webshop\Repository\WebshopOrderLineRepository;
use Flexgrid\Modules\Webshop\Repository\WebshopOrderRepository;
use Flexgrid\Modules\Webshop\Repository\WebshopPaymentTransactionRepository;

class OrderStatusService
{
    public function updateOrderStatus(WebshopOrder $order, string $status): array
    {
        if (!isset($this->getOrderStatusOptions()[$status])) {
            return ['success' => false, 'message' => 'Orderstatus is ongeldig.'];
        }

        $previousStatus = (string)$order->getStatus();
        if ($previousStatus === $status) {
            return ['success' => true, 'order' => $order];
        }

        $order->setStatus($status);
        $order = (new WebshopOrderRepository())->add($order);

        if ($status === 'cancelled') {
            $this->handleCancelled($order);
        }

        if ($status === 'shipped') {
            $this->handleShipped($order);
        }

        if ($status === 'completed') {
            (new OrderMailService())->sendStatusMail($order, 'completed');
        }

        return ['success' => true, 'order' => $order];
    }

    public function updatePaymentStatus(WebshopOrder $order, string $paymentStatus): array
    {
        if (!isset($this->getPaymentStatusOptions()[$paymentStatus])) {
            return ['success' => false, 'message' => 'Betaalstatus is ongeldig.'];
        }

        $previousPaymentStatus = (string)$order->getPaymentStatus();
        if ($previousPaymentStatus === $paymentStatus) {
            return ['success' => true, 'order' => $order];
        }

        $isOnlinePaymentOrder = $this->isOnlinePaymentOrder($order);
        $shouldCancelPendingOrder = $paymentStatus === 'failed' && (string)$order->getStatus() === 'pending';

        $order->setPaymentStatus($paymentStatus);
        if ($paymentStatus === 'paid' && (string)$order->getStatus() === 'pending') {
            $order->setStatus('confirmed');
        }

        if ($shouldCancelPendingOrder) {
            $order->setStatus('cancelled');
        }

        $order = (new WebshopOrderRepository())->add($order);

        if ($paymentStatus === 'paid') {
            if ($isOnlinePaymentOrder) {
                $this->handleOnlinePaymentPaid($order);
            } else {
                (new OrderMailService())->sendStatusMail($order, 'paid');
            }
        }

        if ($paymentStatus === 'refunded') {
            $this->handleRefunded($order);
        }

        if ($shouldCancelPendingOrder) {
            $this->handleCancelled($order, !$isOnlinePaymentOrder);
        }

        return ['success' => true, 'order' => $order];
    }

    public function getOrderStatusOptions(): array
    {
        return [
            'pending' => 'In behandeling',
            'confirmed' => 'Bevestigd',
            'shipped' => 'Verzonden',
            'cancelled' => 'Geannuleerd',
            'completed' => 'Afgerond',
        ];
    }

    public function getPaymentStatusOptions(): array
    {
        return [
            'unpaid' => 'Niet betaald',
            'paid' => 'Betaald',
            'failed' => 'Mislukt',
            'refunded' => 'Terugbetaald',
        ];
    }

    protected function handleOnlinePaymentPaid(WebshopOrder $order): void
    {
        (new DiscountService())->markUsed($order->getWebshopDiscountCodeParent());
        (new InvoiceService())->createForOrder((int)$order->getId());
        (new OrderMailService())->sendOrderCreatedMails($order);
    }

    protected function handleCancelled(WebshopOrder $order, bool $sendMail = true): void
    {
        (new StockService())->reverseOrder($order, (new WebshopOrderLineRepository())->getByOrderId((int)$order->getId()), 'cancelled');
        $this->cancelInvoice($order);

        if ($sendMail) {
            (new OrderMailService())->sendStatusMail($order, 'cancelled');
        }
    }

    protected function handleShipped(WebshopOrder $order): void
    {
        if ((int)$order->getShippedAt() <= 0) {
            $order->setShippedAt(time());
            $order = (new WebshopOrderRepository())->add($order);
        }

        (new OrderMailService())->sendStatusMail($order, 'shipped');
    }

    protected function handleRefunded(WebshopOrder $order): void
    {
        (new StockService())->reverseOrder($order, (new WebshopOrderLineRepository())->getByOrderId((int)$order->getId()), 'refund');
        $this->cancelInvoice($order);
        (new OrderMailService())->sendStatusMail($order, 'refunded');
    }

    protected function cancelInvoice(WebshopOrder $order): void
    {
        $invoiceRepository = new WebshopInvoiceRepository();
        $invoice = $invoiceRepository->findByOrderId((int)$order->getId());
        if (!$invoice || (int)$invoice->getId() <= 0 || (string)$invoice->getStatus() === 'cancelled') {
            return;
        }

        $invoice->setStatus('cancelled');
        $invoiceRepository->add($invoice);
    }

    protected function isOnlinePaymentOrder(WebshopOrder $order): bool
    {
        foreach ((new WebshopPaymentTransactionRepository())->getByOrderId((int)$order->getId()) as $transaction) {
            if ((string)$transaction->getProvider() !== 'manual') {
                return true;
            }
        }

        return false;
    }
}
