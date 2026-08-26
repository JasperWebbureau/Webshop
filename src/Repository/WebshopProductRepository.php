<?php

namespace Flexgrid\Modules\Webshop\Repository;

use Flexgrid\Modules\Webshop\Entity\WebshopProduct;
use Repository\Repository;

class WebshopProductRepository extends Repository
{
    public function getEntity()
    {
        return new WebshopProduct();
    }

    public function getActive($limit = 99, $order = 'title:ASC'): array
    {
        $orderConfig = $this->getOrderConfig($order, 'title');
        $products = $this->select(true)
            ->where('is_active = ? AND status = ?', [1, 'published'])
            ->orderBy($orderConfig['field'], $orderConfig['direction'])
            ->limit(max((int)$limit * 3, (int)$limit))
            ->get();

        return array_slice($this->filterVariantClusters($products), 0, (int)$limit);
    }

    public function search(array $filters = [], int $limit = 100, $order = 'title:ASC'): array
    {
        $query = $this->select(true);
        $orderConfig = $this->getOrderConfig($order, 'title');
        $search = trim((string)($filters['q'] ?? ''));
        $groupId = (int)($filters['group_id'] ?? 0);
        $mainGroupId = (int)($filters['main_group_id'] ?? 0);
        $status = trim((string)($filters['status'] ?? ''));
        $active = trim((string)($filters['active'] ?? ''));
        $color = trim((string)($filters['color'] ?? ''));
        $size = trim((string)($filters['size'] ?? ''));

        if ($search !== '') {
            $query->where(
                '(`title` LIKE CONCAT("%",?,"%") OR `sku` LIKE CONCAT("%",?,"%") OR `short_description` LIKE CONCAT("%",?,"%") OR `color` LIKE CONCAT("%",?,"%") OR `size` LIKE CONCAT("%",?,"%") OR `manufacturer` LIKE CONCAT("%",?,"%"))',
                [$search, $search, $search, $search, $search, $search]
            );
        }

        if ($groupId > 0) {
            $query->where('`group_id` = ?', [$groupId]);
        } elseif ($mainGroupId > 0) {
            $groupIds = (new WebshopProductGroupRepository())->getIdsByMainGroupId($mainGroupId);
            if (empty($groupIds)) {
                return [];
            }

            $query->where('`group_id` IN (' . implode(',', array_fill(0, count($groupIds), '?')) . ')', $groupIds);
        }

        if ($status !== '') {
            $query->where('`status` = ?', [$status]);
        }

        if ($active !== '') {
            $query->where('`is_active` = ?', [(int)$active]);
        }

        if ($color !== '') {
            $query->where('`color` = ?', [$color]);
        }

        if ($size !== '') {
            $query->where('`size` = ?', [$size]);
        }

        return $query
            ->orderBy($orderConfig['field'], $orderConfig['direction'])
            ->limit($limit)
            ->get();
    }

    public function getByGroupId($groupId, $limit = 99, $order = 'title:ASC'): array
    {
        $orderConfig = $this->getOrderConfig($order, 'title');
        $products = $this->select(true)
            ->where('group_id = ? AND is_active = ? AND status = ?', [(int)$groupId, 1, 'published'])
            ->orderBy($orderConfig['field'], $orderConfig['direction'])
            ->limit(max((int)$limit * 3, (int)$limit))
            ->get();

        return array_slice($this->filterVariantClusters($products), 0, (int)$limit);
    }

    public function getByMainGroupId($mainGroupId, $limit = 99, $order = 'title:ASC'): array
    {
        $groupIds = (new WebshopProductGroupRepository())->getIdsByMainGroupId((int)$mainGroupId);
        if (empty($groupIds)) {
            return [];
        }

        $orderConfig = $this->getOrderConfig($order, 'title');
        $products = $this->select(true)
            ->where('group_id IN (' . implode(',', array_fill(0, count($groupIds), '?')) . ') AND is_active = ? AND status = ?', array_merge($groupIds, [1, 'published']))
            ->orderBy($orderConfig['field'], $orderConfig['direction'])
            ->limit(max((int)$limit * 3, (int)$limit))
            ->get();

        return array_slice($this->filterVariantClusters($products), 0, (int)$limit);
    }

    public function filterVariantClusters(array $products): array
    {
        $result = [];
        $seen = [];

        foreach ($products as $product) {
            if (!$product || !method_exists($product, 'getVariantClusterKey')) {
                $result[] = $product;
                continue;
            }

            $key = (string)$product->getVariantClusterKey();
            if ($key === '' || isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $result[] = $product;
        }

        return $result;
    }

    public function getPropertyOptions(string $property): array
    {
        $getter = 'get' . ucfirst($property);
        if (!in_array($property, ['color', 'size'], true)) {
            return [];
        }

        $options = [];
        foreach ($this->setPagination(false)->getAll(500, null, null, true, 'title:ASC') as $product) {
            if (!$product || !method_exists($product, $getter)) {
                continue;
            }

            $value = trim((string)$product->{$getter}());
            if ($value === '') {
                continue;
            }

            $options[$value] = $value;
        }

        ksort($options, SORT_NATURAL | SORT_FLAG_CASE);

        return $options;
    }
}
