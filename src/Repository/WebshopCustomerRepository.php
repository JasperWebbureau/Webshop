<?php

namespace Flexgrid\Modules\Webshop\Repository;

use Flexgrid\Modules\Webshop\Entity\WebshopCustomer;
use Repository\Repository;

class WebshopCustomerRepository extends Repository
{
    public function getEntity()
    {
        return new WebshopCustomer();
    }

    public function getRecent(int $limit = 100): array
    {
        return $this->select(true)
            ->orderBy('id:DESC')
            ->limit($limit)
            ->get();
    }

    public function search(array $filters = [], int $limit = 100): array
    {
        $query = $this->select(true);
        $search = trim((string)($filters['q'] ?? ''));

        if ($search !== '') {
            $query->where(
                '(`title` LIKE CONCAT("%",?,"%") OR `email` LIKE CONCAT("%",?,"%") OR `company_name` LIKE CONCAT("%",?,"%") OR `city` LIKE CONCAT("%",?,"%"))',
                [$search, $search, $search, $search]
            );
        }

        return $query
            ->orderBy('id:DESC')
            ->limit($limit)
            ->get();
    }
}
