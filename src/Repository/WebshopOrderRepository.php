<?php

namespace Flexgrid\Modules\Webshop\Repository;

use Flexgrid\Modules\Webshop\Entity\WebshopOrder;
use Repository\Repository;

class WebshopOrderRepository extends Repository
{
    public function getEntity()
    {
        return new WebshopOrder();
    }

    public function getRecent(int $limit = 50): array
    {
        return $this->select(true)
            ->orderBy('id:DESC')
            ->limit($limit)
            ->get();
    }

    public function getByCustomerId(int $customerId, int $limit = 100): array
    {
        if ($customerId <= 0) {
            return [];
        }

        return $this->select(true)
            ->where('`customer_id` = ?', [$customerId])
            ->orderBy('id:DESC')
            ->limit($limit)
            ->get();
    }

    public function search(array $filters = [], int $limit = 100): array
    {
        $query = $this->select(true);
        $search = trim((string)($filters['q'] ?? ''));
        $status = trim((string)($filters['status'] ?? ''));
        $paymentStatus = trim((string)($filters['payment_status'] ?? ''));

        if ($search !== '') {
            $query->where(
                '(`order_number` LIKE CONCAT("%",?,"%") OR `customer_name` LIKE CONCAT("%",?,"%") OR `customer_email` LIKE CONCAT("%",?,"%"))',
                [$search, $search, $search]
            );
        }

        if ($status !== '') {
            $query->where('`status` = ?', [$status]);
        }

        if ($paymentStatus !== '') {
            $query->where('`payment_status` = ?', [$paymentStatus]);
        }

        return $query
            ->orderBy('id:DESC')
            ->limit($limit)
            ->get();
    }

    public function getDashboardStats(): array
    {
        $orders = $this->getRecent(500);
        $stats = [
            'order_count' => count($orders),
            'pending_count' => 0,
            'paid_count' => 0,
            'revenue_total' => 0.0,
        ];

        foreach ($orders as $order) {
            if ((string)$order->getStatus() === 'pending') {
                $stats['pending_count']++;
            }
            if ((string)$order->getPaymentStatus() === 'paid') {
                $stats['paid_count']++;
                $stats['revenue_total'] += (float)$order->getGrandTotal();
            }
        }

        $stats['revenue_total_formatted'] = '&euro; ' . number_format($stats['revenue_total'], 2, ',', '.');

        return $stats;
    }
}
