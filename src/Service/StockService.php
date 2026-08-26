<?php

namespace Flexgrid\Modules\Webshop\Service;

use Flexgrid\Modules\Webshop\Entity\WebshopOrder;
use Flexgrid\Modules\Webshop\Entity\WebshopProduct;
use Flexgrid\Modules\Webshop\Entity\WebshopStockMutation;
use Flexgrid\Modules\Webshop\Repository\WebshopProductRepository;
use Flexgrid\Modules\Webshop\Repository\WebshopStockMutationRepository;

class StockService
{
    /** @var WebshopProductRepository */
    protected $productRepository;

    /** @var WebshopStockMutationRepository */
    protected $mutationRepository;

    public function __construct(?WebshopProductRepository $productRepository = null, ?WebshopStockMutationRepository $mutationRepository = null)
    {
        $this->productRepository = $productRepository ?: new WebshopProductRepository();
        $this->mutationRepository = $mutationRepository ?: new WebshopStockMutationRepository();
    }

    public function validateItems(array $items): array
    {
        foreach ($items as $item) {
            $product = $this->productRepository->findById((int)$item['product_id']);

            if (!$this->tracksStock($product)) {
                continue;
            }

            if ((int)$product->getStock() < (int)$item['quantity']) {
                return [
                    'success' => false,
                    'message' => 'Er is niet genoeg voorraad voor ' . $product->getTitle() . '.',
                ];
            }
        }

        return ['success' => true];
    }

    public function applyOrder(WebshopOrder $order, array $items): void
    {
        foreach ($items as $item) {
            $product = $this->productRepository->findById((int)$item['product_id']);

            if (!$this->tracksStock($product)) {
                continue;
            }

            $quantity = (int)$item['quantity'];
            $stockBefore = (int)$product->getStock();
            $stockAfter = max(0, $stockBefore - $quantity);

            $product->setStock($stockAfter);
            $this->productRepository->add($product);

            $mutation = new WebshopStockMutation();
            $mutation
                ->setProductId((int)$product->getId())
                ->setOrderId((int)$order->getId())
                ->setQuantityChange(0 - $quantity)
                ->setStockBefore($stockBefore)
                ->setStockAfter($stockAfter)
                ->setReason('order')
                ->setNote((string)$order->getOrderNumber());

            $this->mutationRepository->add($mutation);
        }
    }

    public function reverseOrder(WebshopOrder $order, array $orderLines, string $reason = 'correction'): void
    {
        if ($this->hasReverseMutation($order, $reason)) {
            return;
        }

        foreach ($orderLines as $line) {
            $product = $this->productRepository->findById((int)$line->getProductId());

            if (!$this->tracksStock($product)) {
                continue;
            }

            $quantity = (int)$line->getQuantity();
            $stockBefore = (int)$product->getStock();
            $stockAfter = $stockBefore + $quantity;

            $product->setStock($stockAfter);
            $this->productRepository->add($product);

            $mutation = new WebshopStockMutation();
            $mutation
                ->setProductId((int)$product->getId())
                ->setOrderId((int)$order->getId())
                ->setQuantityChange($quantity)
                ->setStockBefore($stockBefore)
                ->setStockAfter($stockAfter)
                ->setReason($reason === 'refund' ? 'refund' : 'correction')
                ->setNote((string)$order->getOrderNumber() . ' terugboeking: ' . $reason);

            $this->mutationRepository->add($mutation);
        }
    }

    protected function hasReverseMutation(WebshopOrder $order, string $reason): bool
    {
        $expectedReason = $reason === 'refund' ? 'refund' : 'correction';

        foreach ($this->mutationRepository->getByOrderId((int)$order->getId()) as $mutation) {
            if ((int)$mutation->getQuantityChange() > 0 && (string)$mutation->getReason() === $expectedReason) {
                return true;
            }
        }

        return false;
    }

    protected function tracksStock($product): bool
    {
        return $product instanceof WebshopProduct
            && (int)$product->getId() > 0
            && (int)$product->getTrackStock() === 1;
    }
}
