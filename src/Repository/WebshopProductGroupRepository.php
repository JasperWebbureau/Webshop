<?php

namespace Flexgrid\Modules\Webshop\Repository;

use Flexgrid\Modules\Webshop\Entity\WebshopProductGroup;
use Repository\Repository;

class WebshopProductGroupRepository extends Repository
{
    public function getEntity()
    {
        return new WebshopProductGroup();
    }

    public function getActive(): array
    {
        return $this->select(true)
           // ->where(, [1])
            ->orderBy('title:ASC')
            ->get();
    }

    public function getByMainGroupId($mainGroupId, int $limit = 99): array
    {
        if ((int)$mainGroupId <= 0) {
            return $this->getActive();
        }

        return $this->select(true)
            ->where('`main_group_id` = ?', [(int)$mainGroupId])
            ->orderBy('title:ASC')
            ->limit($limit)
            ->get();
    }

    public function getIdsByMainGroupId($mainGroupId): array
    {
        $ids = [];
        foreach ($this->getByMainGroupId((int)$mainGroupId, 999) as $group) {
            if ($group && (int)$group->getId() > 0) {
                $ids[] = (int)$group->getId();
            }
        }

        return $ids;
    }
}
