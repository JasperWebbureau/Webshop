<?php

namespace Flexgrid\Modules\Webshop\Service;

use Flexgrid\Modules\Webshop\Entity\WebshopCustomer;
use Flexgrid\Modules\Webshop\Entity\WebshopOrder;
use Flexgrid\Modules\Webshop\Entity\WebshopOrderLine;
use Flexgrid\Modules\Webshop\Repository\WebshopCustomerRepository;
use Flexgrid\Modules\Webshop\Repository\WebshopOrderLineRepository;
use Flexgrid\Modules\Webshop\Repository\WebshopOrderRepository;

class CheckoutService
{
    /** @var CartService */
    protected $cartService;

    /** @var PriceService */
    protected $priceService;

    /** @var ShippingService */
    protected $shippingService;

    /** @var DiscountService */
    protected $discountService;

    /** @var StockService */
    protected $stockService;

    /** @var TaxService */
    protected $taxService;

    public function __construct(?CartService $cartService = null, ?PriceService $priceService = null, ?ShippingService $shippingService = null, ?DiscountService $discountService = null, ?StockService $stockService = null, ?TaxService $taxService = null)
    {
        $this->cartService = $cartService ?: new CartService();
        $this->priceService = $priceService ?: new PriceService();
        $this->shippingService = $shippingService ?: new ShippingService($this->priceService);
        $this->discountService = $discountService ?: new DiscountService();
        $this->stockService = $stockService ?: new StockService();
        $this->taxService = $taxService ?: new TaxService();
    }

    public function createOrder(array $data): array
    {
        $items = $this->cartService->getItems();
        if (empty($items)) {
            return ['success' => false, 'message' => 'Je winkelwagen is leeg.'];
        }

        $validation = $this->validateCustomerData($data);
        if (!$validation['success']) {
            return $validation;
        }

        $stockValidation = $this->stockService->validateItems($items);
        if (!$stockValidation['success']) {
            return $stockValidation;
        }

        $summary = $this->cartService->getSummary();
        $discount = $this->discountService->resolveDiscount((string)($data['discount_code'] ?? ''), (float)$summary['subtotal']);
        if (!$discount['success']) {
            return $discount;
        }

        $productTaxTotal = $this->calculateTaxTotal($items, (float)$summary['subtotal'], (float)$discount['amount']);
        $shipping = $this->resolveShipping($data, (float)$summary['subtotal']);
        if (!$shipping['success']) {
            return $shipping;
        }
        $shippingTaxRate = $this->taxService->getShippingRate($shipping['method']);
        $shippingTaxTotal = $this->taxService->calculateIncludedTax((float)$shipping['price'], $shippingTaxRate);

        $customer = $this->createCustomer($data);

        $order = new WebshopOrder();
        $order
            ->setOrderNumber($this->generateOrderNumber())
            ->setCustomerId($customer->getId())
            ->setCustomerName($customer->getTitle())
            ->setCustomerEmail($customer->getEmail())
            ->setStatus('pending')
            ->setPaymentStatus('unpaid')
            ->setCurrency('EUR')
            ->setSubtotal((float)$summary['subtotal'])
            ->setTaxTotal($productTaxTotal + $shippingTaxTotal)
            ->setShippingTotal((float)$shipping['price'])
            ->setShippingTaxRate($shippingTaxRate)
            ->setShippingTaxTotal($shippingTaxTotal)
            ->setShippingMethodId((int)$shipping['method']->getId())
            ->setShippingMethodTitle((string)$shipping['method']->getTitle())
            ->setDiscountTotal((float)$discount['amount'])
            ->setDiscountCodeId($discount['discount'] ? (int)$discount['discount']->getId() : 0)
            ->setDiscountCode((string)$discount['code'])
            ->setGrandTotal(max(0.0, (float)$summary['subtotal'] + (float)$shipping['price'] - (float)$discount['amount']))
            ->setCustomerSnapshot($this->buildCustomerSnapshot($customer))
            ->setCustomerNote((string)($data['note'] ?? ''));

        $order = (new WebshopOrderRepository())->add($order);
        $this->createOrderLines($order, $items);
        $this->stockService->applyOrder($order, $items);
        $paymentProvider = $this->resolvePaymentProvider((string)($data['payment_provider'] ?? 'manual'));
        $paymentTransaction = (new PaymentService())->createPendingTransaction($order, $paymentProvider, [
            'source' => 'checkout',
        ]);

        if ($paymentProvider === 'manual') {
            $this->discountService->markUsed($discount['discount']);
            (new OrderMailService())->sendOrderCreatedMails($order);
            $this->cartService->clear();
        }

        return [
            'success' => true,
            'message' => 'Bestelling aangemaakt.',
            'order' => $order,
            'paymentTransaction' => $paymentTransaction,
            'paymentProvider' => $paymentProvider,
        ];
    }

    protected function resolvePaymentProvider(string $provider): string
    {
        if ($provider === 'mollie' && (new MolliePaymentService())->isEnabled()) {
            return 'mollie';
        }

        return 'manual';
    }

    protected function validateCustomerData(array $data): array
    {
        $required = [
            'first_name' => 'Voornaam is verplicht.',
            'last_name' => 'Achternaam is verplicht.',
            'email' => 'E-mailadres is verplicht.',
            'address' => 'Adres is verplicht.',
            'postal_code' => 'Postcode is verplicht.',
            'city' => 'Plaats is verplicht.',
        ];

        foreach ($required as $field => $message) {
            if (trim((string)($data[$field] ?? '')) === '') {
                return ['success' => false, 'message' => $message];
            }
        }

        if (!filter_var((string)$data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Vul een geldig e-mailadres in.'];
        }

        return ['success' => true];
    }

    protected function resolveShipping(array $data, float $subtotal): array
    {
        $methods = $this->shippingService->getAvailableMethods($subtotal);
        if (empty($methods)) {
            return [
                'success' => true,
                'method' => new \Flexgrid\Modules\Webshop\Entity\WebshopShippingMethod(),
                'price' => 0.0,
            ];
        }

        return $this->shippingService->resolveMethod((int)($data['shipping_method_id'] ?? 0), $subtotal);
    }

    protected function createCustomer(array $data): WebshopCustomer
    {
        $customer = new WebshopCustomer();
        $customer
            ->setTitle(trim((string)$data['first_name']) . ' ' . trim((string)$data['last_name']))
            ->setEmail((string)$data['email'])
            ->setCompanyName((string)($data['company_name'] ?? ''))
            ->setPhone((string)($data['phone'] ?? ''))
            ->setAddress((string)$data['address'])
            ->setPostalCode((string)$data['postal_code'])
            ->setCity((string)$data['city'])
            ->setCountry((string)($data['country'] ?? 'NL'));

        return (new WebshopCustomerRepository())->add($customer);
    }

    protected function createOrderLines(WebshopOrder $order, array $items): void
    {
        $repository = new WebshopOrderLineRepository();

        foreach ($items as $item) {
            $product = $item['product'];
            $taxRate = $this->taxService->getProductRate($product);
            $lineTotal = (float)$item['line_total'];

            $line = new WebshopOrderLine();
            $line
                ->setOrderId($order->getId())
                ->setProductId((int)$product->getId())
                ->setProductTitle((string)$product->getTitle())
                ->setSku((string)$product->getSku())
                ->setQuantity((int)$item['quantity'])
                ->setUnitPrice((float)$item['unit_price'])
                ->setTaxRate($taxRate)
                ->setTaxTotal($this->taxService->calculateIncludedTax($lineTotal, $taxRate))
                ->setLineTotal($lineTotal);

            $repository->add($line);
        }
    }

    protected function calculateTaxTotal(array $items, float $subtotal, float $discountTotal): float
    {
        $total = 0.0;
        foreach ($items as $item) {
            $lineTotal = (float)$item['line_total'];
            $lineDiscount = $subtotal > 0 ? $discountTotal * ($lineTotal / $subtotal) : 0;
            $total += $this->taxService->calculateIncludedTax(
                max(0.0, $lineTotal - $lineDiscount),
                $this->taxService->getProductRate($item['product'])
            );
        }

        return $total;
    }

    protected function buildCustomerSnapshot(WebshopCustomer $customer): array
    {
        return [
            'name' => $customer->getTitle(),
            'email' => $customer->getEmail(),
            'company_name' => $customer->getCompanyName(),
            'phone' => $customer->getPhone(),
            'address' => $customer->getAddress(),
            'postal_code' => $customer->getPostalCode(),
            'city' => $customer->getCity(),
            'country' => $customer->getCountry(),
        ];
    }

    protected function generateOrderNumber(): string
    {
        return 'WS-' . date('Ymd') . '-' . strtoupper(substr(uniqid('', false), -6));
    }
}
