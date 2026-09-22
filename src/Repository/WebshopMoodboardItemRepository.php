<?php

namespace Flexgrid\Modules\Webshop\Repository;

use Flexgrid\Modules\Webshop\Entity\WebshopMoodboardItem;
use Repository\Repository;

class WebshopMoodboardItemRepository extends Repository
{
    public function getEntity()
    {
        return new WebshopMoodboardItem();
    }

    public function getByMoodboardId(int $moodboardId): array
    {
        if ($moodboardId <= 0) {
            return [];
        }

        return $this->select(true)
            ->where('`moodboard_id` = ?', [$moodboardId])
            ->orderBy('order', 'ASC')
            ->get();
    }
}
