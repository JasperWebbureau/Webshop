<?php

namespace Flexgrid\Modules\Webshop\Repository;

use Flexgrid\Modules\Webshop\Entity\WebshopProduct;
use Repository\Repository;

class WebshopProductRepository extends Repository
{
    public function getEntity()
    {
        if (class_exists(\App\Webshop\Entity\WebshopProduct::class) && $this->hasAppEntityDefinition(\App\Webshop\Entity\WebshopProduct::class)) {
            return new \App\Webshop\Entity\WebshopProduct();
        }

        return new WebshopProduct();
    }

    protected function hasAppEntityDefinition(string $className): bool
    {
        try {
            $cache = \Flexgrid\Autowire\AutowireEngine::getCache();
            return !empty($cache[$className]['definition']);
        } catch (\Throwable $exception) {
            return false;
        }
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
        $hasLabel = $this->hasAppEntityDefinition(\App\Webshop\Entity\WebshopProduct::class);

        if ($search !== '') {
            $searchFields = ['title', 'sku', 'short_description', 'color', 'size', 'manufacturer'];
            if ($hasLabel) {
                $searchFields[] = 'label';
            }

            $query->where(
                '(' . implode(' OR ', array_map(static function ($field) {
                    return '`' . $field . '` LIKE CONCAT("%",?,"%")';
                }, $searchFields)) . ')',
                array_fill(0, count($searchFields), $search)
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
            ->where('group_id = ?', [(int)$groupId])
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
            ->where('group_id IN (' . implode(',', array_fill(0, count($groupIds), '?')) . ') AND (is_hidden IS NULL OR is_hidden = ?) ', array_merge($groupIds, [0]))
            ->orderBy($orderConfig['field'], $orderConfig['direction'])
            ->limit(max((int)$limit * 3, (int)$limit))
            ->get();

        return array_slice($this->filterVariantClusters($products), 0, (int)$limit);
    }

    /** @return int[] IDs of groups with products counted by the frontend cards. */
    public function getGroupIdsWithProducts(array $groupIds): array
    {
        $groupIds = array_values(array_unique(array_filter(array_map('intval', $groupIds), static function ($id) {
            return $id > 0;
        })));
        if (!$groupIds) {
            return [];
        }

        $products = $this->select(true)
            ->where('`group_id` IN (' . implode(',', array_fill(0, count($groupIds), '?')) . ')', $groupIds)
            ->get();

        $visibleIds = [];
        foreach ($products as $product) {
            if ((int)$product->getIsActive() === 1 && (string)$product->getStatus() === 'published') {
                $visibleIds[(int)$product->getGroupId()] = true;
            }
        }

        return array_keys($visibleIds);
    }

    public function getFirstWithImageByGroupIds(array $groupIds)
    {
        $groupIds = array_values(array_unique(array_filter(array_map('intval', $groupIds), static function ($id) {
            return $id > 0;
        })));
        if (!$groupIds) {
            return null;
        }

        $products = $this->select(true)
            ->where('`group_id` IN (' . implode(',', array_fill(0, count($groupIds), '?')) . ') AND    `image` IS NOT NULL AND `image` != ?', array_merge($groupIds, [ '']))
            ->orderBy('title', 'ASC')
            ->limit(1)
            ->get();

        return $products[0] ?? null;
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
