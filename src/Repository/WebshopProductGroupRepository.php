<?php

namespace Flexgrid\Modules\Webshop\Repository;

use Flexgrid\Modules\Webshop\Entity\WebshopProductGroup;
use Repository\Repository;

class WebshopProductGroupRepository extends Repository
{
    public function getEntity()
    {
        if(class_exists(\App\Webshop\Entity\WebshopProductGroup::class)){
            return new \App\Webshop\Entity\WebshopProductGroup();
        }
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

    public function getWithProducts(int $mainGroupId = 0, int $limit = 99): array
    {
        $groups = $mainGroupId > 0 ? $this->getByMainGroupId($mainGroupId, 9999) : $this->getActive();
        $groups = array_values(array_filter($groups, static function ($group) {
            return $group && (int)$group->getId() > 0 && (int)$group->getIsHidden() !== 1;
        }));
        $groupIds = array_map(static function ($group) { return (int)$group->getId(); }, $groups);
        $productGroupIds = array_fill_keys((new WebshopProductRepository())->getGroupIdsWithProducts($groupIds), true);

        $visible = array_values(array_filter($groups, static function ($group) use ($productGroupIds) {
            return isset($productGroupIds[(int)$group->getId()]);
        }));

        return array_slice($visible, 0, $limit > 0 ? $limit : 99);
    }
}
