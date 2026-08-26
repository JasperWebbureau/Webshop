<?php

namespace Flexgrid\Modules\Webshop\Service;

use Flexgrid\Modules\Webshop\Entity\WebshopProduct;
use Flexgrid\Modules\Webshop\Repository\WebshopProductRepository;

class CartService
{
    private const SESSION_KEY = 'Flexgrid_webshop_cart';

    /** @var WebshopProductRepository */
    protected $productRepository;

    /** @var PriceService */
    protected $priceService;

    public function __construct(?WebshopProductRepository $productRepository = null, ?PriceService $priceService = null)
    {
        $this->productRepository = $productRepository ?: new WebshopProductRepository();
        $this->priceService = $priceService ?: new PriceService();
        $this->ensureCart();
    }

    public function addProduct(int $productId, int $quantity = 1): array
    {
        $quantity = max(1, $quantity);
        $product = $this->productRepository->findById($productId);

        if (!$this->isProductAvailable($product)) {
            return [
                'success' => false,
                'message' => 'Dit product is niet beschikbaar.',
                'cart' => $this->getSummary(),
            ];
        }

        $cartKey = $this->getCartKey($productId);
        $currentQuantity = $this->getQuantity($productId);
        $newQuantity = $currentQuantity + $quantity;

        if (!$this->hasEnoughStock($product, $newQuantity)) {
            return [
                'success' => false,
                'message' => 'Er is niet genoeg voorraad beschikbaar.',
                'cart' => $this->getSummary(),
            ];
        }

        $_SESSION[self::SESSION_KEY]['items'][$cartKey] = [
            'product_id' => $productId,
            'quantity' => $newQuantity,
            'added_at' => $_SESSION[self::SESSION_KEY]['items'][$cartKey]['added_at'] ?? time(),
            'updated_at' => time(),
        ];

        return [
            'success' => true,
            'message' => 'Product toegevoegd aan winkelwagen.',
            'product' => $product,
            'cart' => $this->getSummary(),
        ];
    }

    public function setQuantity(int $productId, int $quantity): array
    {
        $quantity = max(0, $quantity);

        if ($quantity === 0) {
            return $this->removeProduct($productId);
        }

        $product = $this->productRepository->findById($productId);
        if (!$this->isProductAvailable($product)) {
            return [
                'success' => false,
                'message' => 'Dit product is niet beschikbaar.',
                'cart' => $this->getSummary(),
            ];
        }

        if (!$this->hasEnoughStock($product, $quantity)) {
            return [
                'success' => false,
                'message' => 'Er is niet genoeg voorraad beschikbaar.',
                'cart' => $this->getSummary(),
            ];
        }

        $cartKey = $this->getCartKey($productId);
        $_SESSION[self::SESSION_KEY]['items'][$cartKey] = [
            'product_id' => $productId,
            'quantity' => $quantity,
            'added_at' => $_SESSION[self::SESSION_KEY]['items'][$cartKey]['added_at'] ?? time(),
            'updated_at' => time(),
        ];

        return [
            'success' => true,
            'message' => 'Winkelwagen bijgewerkt.',
            'cart' => $this->getSummary(),
        ];
    }

    public function removeProduct(int $productId): array
    {
        unset($_SESSION[self::SESSION_KEY]['items'][$this->getCartKey($productId)]);

        return [
            'success' => true,
            'message' => 'Product verwijderd uit winkelwagen.',
            'cart' => $this->getSummary(),
        ];
    }

    public function clear(): void
    {
        $_SESSION[self::SESSION_KEY] = [
            'items' => [],
            'updated_at' => time(),
        ];
    }

    public function getItems(): array
    {
        $items = [];

        foreach ($_SESSION[self::SESSION_KEY]['items'] as $item) {
            $product = $this->productRepository->findById((int)$item['product_id']);
            if (!$product || (int)$product->getId() <= 0) {
                continue;
            }

            $quantity = max(1, (int)$item['quantity']);
            $items[] = [
                'product' => $product,
                'product_id' => (int)$product->getId(),
                'quantity' => $quantity,
                'unit_price' => $this->priceService->getUnitPrice($product),
                'line_total' => $this->priceService->getLineTotal($product, $quantity),
            ];
        }

        return $items;
    }

    public function getSummary(): array
    {
        $quantity = 0;
        $subtotal = 0.0;

        foreach ($this->getItems() as $item) {
            $quantity += (int)$item['quantity'];
            $subtotal += (float)$item['line_total'];
        }

        return [
            'quantity' => $quantity,
            'subtotal' => $subtotal,
            'subtotal_formatted' => $this->priceService->format($subtotal),
        ];
    }

    protected function getQuantity(int $productId): int
    {
        return (int)($_SESSION[self::SESSION_KEY]['items'][$this->getCartKey($productId)]['quantity'] ?? 0);
    }

    protected function ensureCart(): void
    {
        if (!isset($_SESSION[self::SESSION_KEY]) || !is_array($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = [
                'items' => [],
                'updated_at' => time(),
            ];
        }

        if (!isset($_SESSION[self::SESSION_KEY]['items']) || !is_array($_SESSION[self::SESSION_KEY]['items'])) {
            $_SESSION[self::SESSION_KEY]['items'] = [];
        }
    }

    protected function isProductAvailable($product): bool
    {
        return $product instanceof WebshopProduct
            && (int)$product->getId() > 0
            && (int)$product->getIsActive() === 1
            && (string)$product->getStatus() === 'published';
    }

    protected function hasEnoughStock(WebshopProduct $product, int $quantity): bool
    {
        if ((int)$product->getTrackStock() !== 1) {
            return true;
        }

        return (int)$product->getStock() >= $quantity;
    }

    protected function getCartKey(int $productId): string
    {
        return (string)(int)$productId;
    }
}
