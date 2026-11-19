<?php

if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../tools/bootstrap.php';
require __DIR__ . '/TemporaryAdministrationSchema.php';

use Flexgrid\Database\Connection;
use Flexgrid\Modules\Webshop\Entity\WebshopOrder;
use Flexgrid\Modules\Webshop\Entity\WebshopOrderLine;
use Flexgrid\Modules\Webshop\Integration\Administration\OrderInvoiceMapper;
use Flexgrid\Modules\Webshop\Integration\Administration\AdministrationAdapter;
use Flexgrid\Modules\AdminInvoice\Service\AdminInvoiceFactory;

if (!$webshopDatabaseIsLocal) { throw new RuntimeException('Use a local fixture database.'); }
$db = Connection::getConnections();
TemporaryAdministrationSchema::create($db);
$db->beginTransaction();
$checks = 0;
$assert = static function ($condition, $message) use (&$checks) { ++$checks; if (!$condition) { throw new RuntimeException($message); } };
try {
    $order = (new WebshopOrder())->setId(912345)->setCustomerId(912345)->setOrderNumber('FIXTURE-STANDARD')
        ->setCurrency('EUR')->setSubtotal(12.14)->setDiscountTotal(0.01)->setShippingTotal(0.03)
        ->setShippingTaxRate(21)->setShippingMethodTitle('Verzending')->setGrandTotal(12.16);
    $lines = [
        (new WebshopOrderLine())->setProductTitle('Product 21%')->setQuantity(3)->setUnitPrice(4.03)->setLineTotal(12.09)->setTaxRate(21),
        (new WebshopOrderLine())->setProductTitle('Product 9%')->setQuantity(1)->setUnitPrice(0.05)->setLineTotal(0.05)->setTaxRate(9),
    ];
    $mapper = new OrderInvoiceMapper();
    $pricing = $mapper->map($order, $lines);
    $assert($pricing['gross'] === 1216 && $pricing['net'] + $pricing['tax'] === 1216, 'Mixed VAT and shipping close in cents');
    $snapshot = ['name' => 'Fixture klant', 'company_name' => '', 'email' => 'fixture@example.invalid', 'phone' => '',
        'address' => 'Teststraat 1', 'postal_code' => '1234AB', 'city' => 'Teststad', 'country' => 'NL'];
    $job = ['pricing' => $pricing, 'shop_id' => 'fixture-standard', 'issue_date' => '2026-11-17',
        'booked_on' => '2026-11-17', 'payment_reference' => 'fixture-standard-payment'];
    $adapter = new AdministrationAdapter();
    $first = $adapter->post($order, $snapshot, $job);
    $second = $adapter->post($order, $snapshot, $job);
    $assert($first === $second, 'Identical retries return the same customer and invoice');
    foreach (['admin_customer', 'admin_invoice', 'admin_payment', 'admin_payment_allocation'] as $table) {
        $assert((int)$db->query('SELECT COUNT(*) FROM ' . $table)->fetchColumn() === 1, $table . ' is idempotent');
    }
    $invoice = AdminInvoiceFactory::createGetInvoice()->execute($first['invoice_public_id']);
    $assert($invoice->getPaymentStatus()->getValue() === 'paid', 'AdminInvoice is paid');
    $assert($invoice->getLines()[0]->getQuantity()->getScaledValue() === 30000, 'Original quantity is preserved');
    $assert($invoice->getLines()[2]->calculateGross()->getMinorUnits() === 3, 'Three-cent inclusive shipping survives persistence');
    $order->setGrandTotal(12.17);
    try { $mapper->map($order, $lines); throw new RuntimeException('Mismatch accepted'); }
    catch (DomainException $expected) { ++$checks; }
    echo $checks . " administration checks passed; all financial records rolled back.\n";
} finally { $db->rollBack(); }
