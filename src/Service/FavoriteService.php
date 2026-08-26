<?php

namespace Flexgrid\Modules\Webshop\Service;

use Flexgrid\Modules\Webshop\Repository\WebshopProductRepository;

class FavoriteService
{
    private const SESSION_KEY = 'Flexgrid_webshop_favorites';

    public function __construct()
    {
        $this->ensureFavorites();
    }

    public function toggleProduct(int $productId): array
    {
        $productId = max(0, $productId);
        if ($productId <= 0) {
            return [
                'success' => false,
                'message' => 'Product niet gevonden.',
                'isFavorite' => false,
                'favorites' => $this->getProductIds(),
            ];
        }

        if ($this->hasProduct($productId)) {
            $this->removeProduct($productId);
            return [
                'success' => true,
                'message' => 'Product verwijderd uit favorieten.',
                'isFavorite' => false,
                'favorites' => $this->getProductIds(),
            ];
        }

        $this->addProduct($productId);
        return [
            'success' => true,
            'message' => 'Product toegevoegd aan favorieten.',
            'isFavorite' => true,
            'favorites' => $this->getProductIds(),
        ];
    }

    public function addProduct(int $productId): void
    {
        $productId = max(0, $productId);
        if ($productId <= 0) {
            return;
        }

        $_SESSION[self::SESSION_KEY]['product_ids'][(string)$productId] = $productId;
        $_SESSION[self::SESSION_KEY]['updated_at'] = time();
    }

    public function removeProduct(int $productId): void
    {
        unset($_SESSION[self::SESSION_KEY]['product_ids'][(string)(int)$productId]);
        $_SESSION[self::SESSION_KEY]['updated_at'] = time();
    }

    public function hasProduct(int $productId): bool
    {
        return isset($_SESSION[self::SESSION_KEY]['product_ids'][(string)(int)$productId]);
    }

    public function getProductIds(): array
    {
        return array_values(array_map('intval', $_SESSION[self::SESSION_KEY]['product_ids']));
    }

    public function getItems(): array
    {
        $items = [];
        $repository = new WebshopProductRepository();

        foreach ($this->getProductIds() as $productId) {
            $product = $repository->findById((int)$productId);
            if (
                !$product ||
                (int)$product->getId() <= 0 ||
                (int)$product->getIsActive() !== 1 ||
                (string)$product->getStatus() !== 'published'
            ) {
                continue;
            }

            $items[] = $product;
        }

        return $items;
    }

    public function getSummary(): array
    {
        return [
            'quantity' => count($this->getItems()),
        ];
    }

    private function ensureFavorites(): void
    {
        if (!isset($_SESSION[self::SESSION_KEY]) || !is_array($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = [
                'product_ids' => [],
                'updated_at' => time(),
            ];
        }

        if (!isset($_SESSION[self::SESSION_KEY]['product_ids']) || !is_array($_SESSION[self::SESSION_KEY]['product_ids'])) {
            $_SESSION[self::SESSION_KEY]['product_ids'] = [];
        }
    }
}
