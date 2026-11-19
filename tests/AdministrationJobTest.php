<?php

if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../tools/bootstrap.php';
require __DIR__ . '/TemporaryAdministrationSchema.php';

use Flexgrid\Database\Connection;
use Flexgrid\Modules\Webshop\Entity\WebshopOrder;
use Flexgrid\Modules\Webshop\Entity\WebshopOrderLine;
use Flexgrid\Modules\Webshop\Repository\WebshopOrderRepository;
use Flexgrid\Modules\Webshop\Repository\WebshopOrderLineRepository;
use Flexgrid\Modules\Webshop\Service\AdministrationService;
use Flexgrid\Modules\Webshop\Service\PaymentService;

if (!$webshopDatabaseIsLocal) { throw new RuntimeException('Local temporary fixtures only.'); }
$db = Connection::getConnections();
TemporaryAdministrationSchema::create($db);
foreach (['webshoporder', 'webshoporderline', 'webshoppaymenttransaction'] as $table) {
    $schema = $db->query('SHOW CREATE TABLE `' . $table . '`')->fetch(\PDO::FETCH_NUM)[1];
    $db->exec(preg_replace('/^CREATE TABLE/', 'CREATE TEMPORARY TABLE', $schema));
}
$orders = new WebshopOrderRepository();
$service = new AdministrationService();
$checks = 0;
$assert = static function ($condition, $message) use (&$checks) { ++$checks; if (!$condition) { throw new RuntimeException($message); } };
$order = (new WebshopOrder())->setOrderNumber('FIXTURE-JOB')->setCustomerId(999999)->setCustomerName('Fixture')
    ->setCustomerEmail('fixture@example.invalid')->setCurrency('EUR')->setStatus('pending')->setPaymentStatus('unpaid')
    ->setSubtotal(0.03)->setDiscountTotal(0)->setShippingTotal(0)->setShippingTaxRate(0)->setGrandTotal(0.03)
    ->setCustomerSnapshot(['name' => 'Fixture', 'company_name' => '', 'email' => 'fixture@example.invalid', 'phone' => '',
        'address' => 'Teststraat 1', 'postal_code' => '1234AB', 'city' => 'Teststad', 'country' => 'NL']);
$order = $orders->add($order);
(new WebshopOrderLineRepository())->add((new WebshopOrderLine())->setOrderId($order->getId())->setProductId(999999)
    ->setProductTitle('Fixture')->setQuantity(1)->setUnitPrice(0.03)->setLineTotal(0.03)->setTaxRate(21)->setTaxTotal(0.01));
$service->prepare($order);
$service->process((int)$order->getId());
$assert((int)$db->query('SELECT COUNT(*) FROM admin_invoice')->fetchColumn() === 0, 'Unpaid order creates no invoice');
$transaction = (new PaymentService())->createPendingTransaction($order, 'manual');
// Persist the paid fixture directly: no status mails, provider calls or real payment.
$db->exec("UPDATE webshoporder SET payment_status = 'paid'");
$db->exec("UPDATE webshoppaymenttransaction SET status = 'paid', paid_at = " . time());
$service->process((int)$order->getId());
$read = static function () use ($db) { return json_decode($db->query('SELECT customer_snapshot FROM webshoporder')->fetchColumn(), true)['administration']; };
$job = $read();
$assert($job['status'] === 'completed' && !empty($job['invoice_number']), 'Paid order completes the durable job');
$service->process((int)$order->getId());
$assert((int)$db->query('SELECT COUNT(*) FROM admin_invoice')->fetchColumn() === 1, 'Completed retry makes no second invoice');
$assert((int)$db->query('SELECT COUNT(*) FROM admin_payment')->fetchColumn() === 1, 'Completed retry makes no second payment');
$snapshot = json_decode($db->query('SELECT customer_snapshot FROM webshoporder')->fetchColumn(), true);
$snapshot['administration']['status'] = 'awaiting_payment';
$snapshot['administration']['tenant_id'] = 'invalid-fixture-tenant';
$update = $db->prepare('UPDATE webshoporder SET customer_snapshot = ?');
$update->execute([json_encode($snapshot)]);
$service->process((int)$order->getId());
$assert($read()['status'] === 'failed' && !empty($read()['error']), 'Tenant mismatch persists a visible failure');
$snapshot['administration']['tenant_id'] = __ADMIN_TENANT_ID__;
$update->execute([json_encode($snapshot)]);
$service->process((int)$order->getId());
$assert($read()['status'] === 'completed', 'Repair reuses the original invoice and payment');
$assert((int)$db->query('SELECT COUNT(*) FROM admin_invoice')->fetchColumn() === 1, 'Repair remains idempotent');
echo $checks . " durable administration checks passed; only temporary tables used.\n";
