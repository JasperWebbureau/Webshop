<?php

namespace Flexgrid\Modules\Webshop\Service {
    // No mail can leave this fixture, including through payment-status callbacks.
    final class OrderMailService {
        public function sendOrderCreatedMails($order): void {}
        public function sendStatusMail($order, $status): void {}
    }
}
namespace {
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../tools/bootstrap.php';
require __DIR__ . '/TemporaryAdministrationSchema.php';

use Flexgrid\Database\Connection;
use Flexgrid\Modules\Webshop\Entity\WebshopProduct;
use Flexgrid\Modules\Webshop\Repository\WebshopProductRepository;
use Flexgrid\Modules\Webshop\Service\CartService;
use Flexgrid\Modules\Webshop\Service\ShippingService;
use Flexgrid\Modules\Webshop\Service\DiscountService;
use Flexgrid\Modules\Webshop\Service\TaxService;
use Flexgrid\Modules\Webshop\Service\CheckoutService;
use Flexgrid\Modules\Webshop\Service\PaymentService;

if (!$webshopDatabaseIsLocal) { throw new RuntimeException('Local temporary fixtures only.'); }
$db = Connection::getConnections();
TemporaryAdministrationSchema::create($db);
foreach (['webshopproduct', 'webshopcustomer', 'webshoporder', 'webshoporderline', 'webshoppaymenttransaction', 'webshopstockmutation', 'webshopinvoice'] as $table) {
    $schema = $db->query('SHOW CREATE TABLE `' . $table . '`')->fetch(PDO::FETCH_NUM)[1];
    $db->exec(preg_replace('/^CREATE TABLE/', 'CREATE TEMPORARY TABLE', $schema));
}
$product = (new WebshopProduct())->setTitle('Checkout fixture')->setPrice(4.03)->setStock(5)->setTrackStock(1);
$product = (new WebshopProductRepository())->add($product);
$cart = new class($product) extends CartService {
    private $product;
    public $quantity = 3;
    public $cleared = false;
    public function __construct($product) { $this->product = $product; }
    public function getItems(): array { return [['product_id' => $this->product->getId(), 'product' => $this->product, 'quantity' => $this->quantity, 'unit_price' => 999, 'line_total' => 2997]]; }
    public function clear(): void { $this->cleared = true; }
};
$shipping = new class extends ShippingService { public function getAvailableMethods(float $subtotal = 0): array { return []; } };
$discount = new class extends DiscountService {
    public function resolveDiscount(string $code, float $subtotal): array { return ['success' => true, 'amount' => 0.0, 'code' => '', 'discount' => null]; }
    public function markUsed($discount): void {}
};
$tax = new class extends TaxService {
    public function getProductRate(WebshopProduct $product): float { return 21.0; }
    public function getShippingRate(\Flexgrid\Modules\Webshop\Entity\WebshopShippingMethod $method): float { return 21.0; }
};
$service = new CheckoutService($cart, null, $shipping, $discount, null, $tax);
$data = ['first_name' => 'Fixture', 'last_name' => 'Checkout', 'email' => 'fixture@example.invalid',
    'address' => 'Teststraat 1', 'postal_code' => '1234AB', 'city' => 'Teststad', 'country' => 'NL', 'payment_provider' => 'manual'];
$checks = 0;
$assert = static function ($condition, $message) use (&$checks) { ++$checks; if (!$condition) { throw new RuntimeException($message); } };
$result = $service->createOrder($data);
$assert(!empty($result['success']), 'Standard checkout succeeds: ' . ($result['message'] ?? ''));
$assert((float)$result['order']->getGrandTotal() === 12.09, 'Checkout uses locked current prices');
$assert((int)$db->query('SELECT stock FROM webshopproduct')->fetchColumn() === 2, 'Checkout reserves exactly three products');
$assert($cart->cleared, 'Manual checkout clears the cart after commit');
$assert((int)$db->query('SELECT COUNT(*) FROM admin_invoice')->fetchColumn() === 0, 'No invoice before payment');
$paymentService = new PaymentService();
$paymentService->updateTransactionStatus((int)$result['paymentTransaction']->getId(), 'paid');
$paymentService->updateTransactionStatus((int)$result['paymentTransaction']->getId(), 'paid');
$assert((int)$db->query('SELECT COUNT(*) FROM admin_invoice')->fetchColumn() === 1, 'Paid checkout gets one AdminInvoice');
$assert((int)$db->query('SELECT COUNT(*) FROM admin_payment')->fetchColumn() === 1, 'Repeated paid status gets one receipt');
$assert((int)$db->query('SELECT COUNT(*) FROM webshopinvoice')->fetchColumn() === 0, 'No legacy invoice is generated');
$paymentService->updateTransactionStatus((int)$result['paymentTransaction']->getId(), 'pending');
$assert($db->query('SELECT status FROM webshoppaymenttransaction')->fetchColumn() === 'paid', 'Late pending notification cannot undo paid');
$cart->quantity = 9;
$rejected = $service->createOrder($data);
$assert(empty($rejected['success']) && (int)$db->query('SELECT COUNT(*) FROM webshoporder')->fetchColumn() === 1, 'Insufficient stock rolls back the checkout');
$assert((int)$db->query('SELECT COUNT(*) FROM webshopcustomer')->fetchColumn() === 1, 'Failed checkout leaves no customer');
echo $checks . " checkout checks passed; no real payments or mails, only temporary tables.\n";
}
