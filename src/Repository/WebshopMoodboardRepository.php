<?php

namespace Flexgrid\Modules\Webshop\Repository;

use Flexgrid\Modules\Webshop\Entity\WebshopMoodboard;
use Repository\Repository;

class WebshopMoodboardRepository extends Repository
{
    public function getEntity()
    {
        return new WebshopMoodboard();
    }

    public function getActive($limit = 99): array
    {
        return $this->select(true)
            ->where('(`is_active` = ? OR `is_active` IS NULL)', [1])
            ->orderBy('order', 'ASC')
            ->limit((int)$limit)
            ->get();
    }

    public function search(string $query = '', int $limit = 100): array
    {
        $select = $this->select(true);
        $query = trim($query);

        if ($query !== '') {
            $select->where('`title` LIKE CONCAT("%",?,"%") OR `eyebrow` LIKE CONCAT("%",?,"%")', [$query, $query]);
        }

        return $select
            ->orderBy('order', 'ASC')
            ->limit($limit)
            ->get();
    }
}
