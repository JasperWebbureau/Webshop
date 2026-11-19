<?php

namespace Flexgrid\Modules\Webshop\Service;

use Flexgrid\Database\Connection;
use Flexgrid\Modules\Webshop\Integration\Administration\AdministrationAdapter;
use Flexgrid\Modules\Webshop\Integration\Administration\OrderInvoiceMapper;
use Flexgrid\Modules\Webshop\Repository\WebshopOrderRepository;
use Flexgrid\Modules\Webshop\Repository\WebshopOrderLineRepository;
use Flexgrid\Modules\Webshop\Repository\WebshopPaymentTransactionRepository;

/** Durable order-local administration job; no project subclasses or extra schema. */
final class AdministrationService
{
    public function prepare($order): void
    {
        $tenant = defined('__ADMIN_TENANT_ID__') ? trim((string)__ADMIN_TENANT_ID__) : '';
        $shop = defined('__WEBSHOP_SHOP_ID__') ? trim((string)__WEBSHOP_SHOP_ID__) : '';
        if ($tenant === '' || $shop === '') { throw new \DomainException('Stel de administratie-tenant en een unieke webshop-id in.'); }
        $pricing = (new OrderInvoiceMapper())->map($order, (new WebshopOrderLineRepository())->getByOrderId((int)$order->getId()));
        $snapshot = json_decode($order->getCustomerSnapshot(), true);
        $snapshot['administration'] = ['version' => 1, 'tenant_id' => $tenant, 'shop_id' => $shop,
            'status' => 'awaiting_payment', 'pricing' => $pricing];
        $order->setCustomerSnapshot($snapshot)->setTaxTotal($pricing['tax'] / 100);
        (new WebshopOrderRepository())->add($order);
    }

    public function process(int $orderId): void
    {
        $db = Connection::getConnections();
        if ($db->inTransaction()) { throw new \LogicException('Administratie wordt na de betaalcommit verwerkt.'); }
        $db->beginTransaction();
        try {
            $statement = $db->prepare('SELECT * FROM webshoporder WHERE id = ? FOR UPDATE');
            $statement->execute([$orderId]);
            $row = $statement->fetch(\PDO::FETCH_ASSOC);
            if (!$row) { throw new \DomainException('Order niet gevonden.'); }
            $order = (new WebshopOrderRepository())->getEntity();
            $order->populate($row);
            $snapshot = json_decode($order->getCustomerSnapshot(), true) ?: [];
            $job = $snapshot['administration'] ?? null;
            if (!$job || ($job['status'] ?? '') === 'completed' || $order->getPaymentStatus() !== 'paid') { $db->commit(); return; }
            if (($job['tenant_id'] ?? '') !== (defined('__ADMIN_TENANT_ID__') ? (string)__ADMIN_TENANT_ID__ : '')
                || ($job['shop_id'] ?? '') !== (defined('__WEBSHOP_SHOP_ID__') ? (string)__WEBSHOP_SHOP_ID__ : '')) {
                throw new \DomainException('De administratie-tenant of webshop-id wijkt af van deze order.');
            }
            $paid = null;
            foreach ((new WebshopPaymentTransactionRepository())->getByOrderId($orderId) as $transaction) {
                if ($transaction->getStatus() === 'paid') { $paid = $transaction; break; }
            }
            if (!$paid || (int)round((float)$paid->getAmount() * 100) !== $job['pricing']['gross'] || $paid->getCurrency() !== $order->getCurrency()) {
                throw new \DomainException('Een passende betaalde transactie ontbreekt.');
            }
            $metadata = json_decode($paid->getMetadata(), true) ?: [];
            if ($paid->getProvider() !== 'manual' && empty($metadata['verified_at'])) { throw new \DomainException('Online betaling is nog niet geverifieerd.'); }
            if (($metadata['mode'] ?? '') === 'test' && (!defined('__INDEV__') || __INDEV__ !== true)) { throw new \DomainException('Testbetalingen worden alleen in een ontwikkelinstallatie geboekt.'); }
            $timezone = new \DateTimeZone('Europe/Amsterdam');
            $job['issue_date'] = $job['issue_date'] ?? (new \DateTimeImmutable('now', $timezone))->format('Y-m-d');
            $job['booked_on'] = (new \DateTimeImmutable('@' . (int)$paid->getPaidAt()))->setTimezone($timezone)->format('Y-m-d');
            $job['payment_reference'] = $paid->getProviderReference() ?: $paid->getTransactionReference();
            $job['test'] = ($metadata['mode'] ?? '') === 'test';
            $result = (new AdministrationAdapter())->post($order, $snapshot, $job);
            $snapshot['administration'] = array_merge($job, $result, ['status' => 'completed']);
            unset($snapshot['administration']['error']);
            $order->setCustomerSnapshot($snapshot);
            (new WebshopOrderRepository())->add($order);
            $db->commit();
        } catch (\Throwable $exception) {
            if ($db->inTransaction()) { $db->rollBack(); }
            // Persist failure separately, without overwriting a concurrent successful retry.
            $db->beginTransaction();
            try {
                $statement = $db->prepare('SELECT customer_snapshot FROM webshoporder WHERE id = ? FOR UPDATE');
                $statement->execute([$orderId]);
                $snapshot = json_decode((string)$statement->fetchColumn(), true) ?: [];
                if (isset($snapshot['administration']) && $snapshot['administration']['status'] !== 'completed') {
                    $snapshot['administration']['status'] = 'failed';
                    $snapshot['administration']['error'] = $exception instanceof \DomainException ? $exception->getMessage() : 'Administratieve verwerking mislukt: ' . get_class($exception);
                    $statement = $db->prepare('UPDATE webshoporder SET customer_snapshot = ? WHERE id = ?');
                    $statement->execute([json_encode($snapshot, JSON_UNESCAPED_UNICODE), $orderId]);
                }
                $db->commit();
            } catch (\Throwable $failure) { if ($db->inTransaction()) { $db->rollBack(); } throw $failure; }
        }
    }
}
