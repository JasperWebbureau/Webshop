<?php

namespace Flexgrid\Modules\Webshop\Service;

use Flexgrid\Modules\Webshop\Entity\WebshopOrder;
use Flexgrid\Modules\Webshop\Entity\WebshopPaymentTransaction;
use Flexgrid\Modules\Webshop\Repository\WebshopOrderRepository;
use Flexgrid\Modules\Webshop\Repository\WebshopPaymentTransactionRepository;

class PaymentService
{
    public function createPendingTransaction(WebshopOrder $order, string $provider = 'manual', array $metadata = []): WebshopPaymentTransaction
    {
        $transaction = new WebshopPaymentTransaction();
        $transaction
            ->setOrderId((int)$order->getId())
            ->setTransactionReference($this->generateTransactionReference())
            ->setProvider($provider)
            ->setProviderReference('')
            ->setStatus('pending')
            ->setAmount((float)$order->getGrandTotal())
            ->setCurrency((string)$order->getCurrency())
            ->setCustomerEmail((string)$order->getCustomerEmail())
            ->setPaidAt(0)
            ->setMetadata($metadata);

        return (new WebshopPaymentTransactionRepository())->add($transaction);
    }

    public function updateTransactionStatus(int $transactionId, string $status, string $providerReference = ''): array
    {
        if (!isset($this->getStatusOptions()[$status])) {
            return ['success' => false, 'message' => 'Betaalstatus is ongeldig.'];
        }

        $transactionRepository = new WebshopPaymentTransactionRepository();
        $transaction = $transactionRepository->findById($transactionId);
        if (!$transaction || (int)$transaction->getId() <= 0) {
            return ['success' => false, 'message' => 'Transactie niet gevonden.'];
        }

        $transaction->setStatus($status);
        if ($providerReference !== '') {
            $transaction->setProviderReference($providerReference);
        }
        if ($status === 'paid' && (int)$transaction->getPaidAt() <= 0) {
            $transaction->setPaidAt(time());
        }

        $transaction = $transactionRepository->add($transaction);
        $this->syncOrderPaymentStatus($transaction);

        return [
            'success' => true,
            'message' => 'Betaaltransactie bijgewerkt.',
            'transaction' => $transaction,
        ];
    }

    public function getStatusOptions(): array
    {
        return [
            'pending' => 'In afwachting',
            'paid' => 'Betaald',
            'failed' => 'Mislukt',
            'cancelled' => 'Geannuleerd',
            'refunded' => 'Terugbetaald',
        ];
    }

    protected function syncOrderPaymentStatus(WebshopPaymentTransaction $transaction): void
    {
        $orderRepository = new WebshopOrderRepository();
        $order = $orderRepository->findById((int)$transaction->getOrderId());
        if (!$order || (int)$order->getId() <= 0) {
            return;
        }

        $map = [
            'pending' => 'unpaid',
            'paid' => 'paid',
            'failed' => 'failed',
            'cancelled' => 'failed',
            'refunded' => 'refunded',
        ];

        (new OrderStatusService())->updatePaymentStatus($order, $map[$transaction->getStatus()] ?? 'unpaid');
    }

    protected function generateTransactionReference(): string
    {
        return 'PAY-' . date('Ymd') . '-' . strtoupper(substr(uniqid('', false), -6));
    }
}
