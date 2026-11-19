<?php

namespace Flexgrid\Modules\Webshop\Service;

use Flexgrid\App\Settings\Settings;
use Flexgrid\Modules\Webshop\Controller\WebshopController;
use Flexgrid\Modules\Webshop\Entity\WebshopOrder;
use Flexgrid\Modules\Webshop\Entity\WebshopPaymentTransaction;
use Flexgrid\Modules\Webshop\Repository\WebshopPaymentTransactionRepository;
use Flexgrid\Utils\_Price;
use Flexgrid\Utils\Payments\Mollie\Mollie;

class MolliePaymentService
{
    public function getMode(): string
    {
        $mode = Settings::get('webshop_mollie_mode', ['value' => 'test', 'label' => 'Mollie-modus']);
        return ($mode['value'] ?? '') === 'live' ? 'live' : 'test';
    }

    public function isEnabled(): bool
    {
        new Settings();
        $setting = Settings::get('webshop_mollie_enabled', [
            'value' => 0,
            'label' => 'Webshop Mollie inschakelen',
        ]);

        return (int)($setting['value'] ?? 0) === 1;
    }

    public function startPayment(WebshopOrder $order, WebshopPaymentTransaction $transaction): array
    {
        if (!$this->isEnabled()) {
            return ['success' => false, 'message' => 'Mollie is niet ingeschakeld.'];
        }

        if ((int)$order->getId() <= 0 || (int)$transaction->getId() <= 0) {
            return ['success' => false, 'message' => 'Order of transactie ontbreekt.'];
        }

        $mollie = $this->createMollieClient($order, $transaction);
        $existingPaymentId = trim((string)$transaction->getProviderReference());

        if ($existingPaymentId !== '') {
            try {
                $existingPayment = $mollie->getPaymentById($existingPaymentId);
                if (in_array((string)$existingPayment->status, ['open', 'pending', 'authorized'], true)) {
                    return [
                        'success' => true,
                        'checkoutUrl' => $mollie->getPaymentUrl($existingPayment),
                        'transaction' => $transaction,
                    ];
                }
            } catch (\Throwable $exception) {
                // Existing provider payment cannot be reused; create a new one below.
            }
        }

        try {
            $payment = $mollie->getPayment();
            $transaction
                ->setProvider('mollie')
                ->setProviderReference((string)$payment->id)
                ->setStatus($this->mapMollieStatus((string)$payment->status));

            $transaction = (new WebshopPaymentTransactionRepository())->add($transaction);

            return [
                'success' => true,
                'checkoutUrl' => $mollie->getPaymentUrl($payment),
                'transaction' => $transaction,
            ];
        } catch (\Throwable $exception) {
            return [
                'success' => false,
                'message' => $exception->getMessage(),
            ];
        }
    }

    public function syncPayment(string $paymentId): array
    {
        $paymentId = trim($paymentId);
        if ($paymentId === '') {
            return ['success' => false, 'message' => 'Payment id ontbreekt.'];
        }

        $repository = new WebshopPaymentTransactionRepository();
        $transaction = $repository->findByReference($paymentId);
        if (!$transaction || (int)$transaction->getId() <= 0) {
            return ['success' => false, 'message' => 'Transactie niet gevonden.'];
        }

        $order = $transaction->getWebshopOrderParent();
        if (!$order || (int)$order->getId() <= 0) {
            return ['success' => false, 'message' => 'Order niet gevonden.'];
        }

        try {
            $payment = $this->createMollieClient($order, $transaction)->getPaymentById($paymentId);
            if ((string)$payment->id !== $paymentId
                || (string)$payment->amount->currency !== (string)$transaction->getCurrency()
                || (string)$payment->amount->value !== number_format((float)$transaction->getAmount(), 2, '.', '')
                || number_format((float)$order->getGrandTotal(), 2, '.', '') !== (string)$payment->amount->value) {
                throw new \DomainException('Mollie-betaling wijkt af van de opgeslagen order.');
            }
            $metadata = json_decode($transaction->getMetadata(), true) ?: [];
            if (isset($payment->mode) && $payment->mode !== ($metadata['mode'] ?? '')) {
                throw new \DomainException('Mollie-modus wijkt af van de opgeslagen betaling.');
            }
            if ($payment->status === 'paid' && (empty($payment->paidAt) || strtotime($payment->paidAt) === false)) {
                throw new \DomainException('Mollie heeft geen geldige betaaldatum bevestigd.');
            }
            $metadata['verified_at'] = time();
            $metadata['provider_paid_at'] = (string)($payment->paidAt ?? '');
            $transaction->setMetadata($metadata);
            (new WebshopPaymentTransactionRepository())->add($transaction);
            return (new PaymentService())->updateTransactionStatus(
                (int)$transaction->getId(),
                $this->mapMollieStatus((string)$payment->status),
                $paymentId
            );
        } catch (\Throwable $exception) {
            return [
                'success' => false,
                'message' => $exception->getMessage(),
            ];
        }
    }

    public function getPaymentMethods(): array
    {
        $methods = [
            'manual' => t('webshop_payment_method_manual', 'Handmatig / achteraf betalen'),
        ];

        if ($this->isEnabled()) {
            $methods['mollie'] = t('webshop_payment_method_mollie', 'Online betalen via Mollie');
        }

        return $methods;
    }

    protected function createMollieClient(WebshopOrder $order, WebshopPaymentTransaction $transaction): Mollie
    {
        $metadata = json_decode($transaction->getMetadata(), true) ?: [];
        if (!in_array($metadata['mode'] ?? '', ['test', 'live'], true)) {
            throw new \DomainException('De opgeslagen Mollie-modus ontbreekt.');
        }
        return (new Mollie($metadata['mode']))
            ->setPrice(new _Price(number_format((float)$transaction->getAmount(), 2, '.', '')))
            ->withCurrency((string)$transaction->getCurrency())
            ->setDescription('Webshop order ' . $order->getOrderNumber())
            ->setRedirectUrl(WebshopController::getPaymentReturnUrl((int)$transaction->getId()) . '&return_token=' . rawurlencode((string)$metadata['return_token']))
            ->setWebhookUrl(rtrim(__DOMAIN__, '/') . '/api/WebshopPayment/webhook');
    }

    protected function mapMollieStatus(string $status): string
    {
        $map = [
            'paid' => 'paid',
            'open' => 'pending',
            'pending' => 'pending',
            'authorized' => 'pending',
            'failed' => 'failed',
            'expired' => 'failed',
            'canceled' => 'cancelled',
            'cancelled' => 'cancelled',
            'refunded' => 'refunded',
            'charged_back' => 'refunded',
        ];

        return $map[$status] ?? 'pending';
    }
}
