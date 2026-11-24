<?php

namespace Flexgrid\Modules\Webshop\Repository;

use Flexgrid\Modules\Webshop\Entity\WebshopProduct;
use Repository\Repository;

class WebshopProductRepository extends Repository
{
    public function add($entity)
    {
        if (defined('__DOMAIN__') && strcasecmp((string)parse_url(__DOMAIN__, PHP_URL_HOST), 'localhost') === 0) {
            if (is_array($entity)) {
                $data = $entity;
                $entity = $this->getEntity();
                $entity->populate($data);
            }

            if ($entity instanceof WebshopProduct && (int)$entity->getId() <= 0) {
                $entity->setIsHidden(0);
            }
        }

        return parent::add($entity);
    }

    public function findForStockUpdate(int $id)
    {
        $db = \Flexgrid\Database\Connection::getConnections();
        $statement = $db->prepare('SELECT * FROM webshopproduct WHERE id = ?' . ($db->inTransaction() ? ' FOR UPDATE' : ''));
        $statement->execute([$id]);
        $row = $statement->fetch(\PDO::FETCH_ASSOC);
        if (!$row) { return null; }
        $product = $this->getEntity();
        $product->populate($row);
        return $product;
    }

    public function getCatalogPage(int $limit, string $filterQuery, string $order, int $page): array
    {
        $limit = max(1, min(100, $limit));
        // Cluster before slicing: a fixed multiplier can skip products between pages.
        $products = $this->filterVariantClusters($this->setPagination(false)->getAll(0, null, $filterQuery, false, $order));
        $records = count($products);
        $pages = (int)ceil($records / $limit);
        $page = min(max(0, $page), max(0, $pages - 1));
        return ['entities' => array_slice($products, $page * $limit, $limit), 'pagination' => [
            'records' => $records, 'currentPage' => $page, 'offset' => $page * $limit,
            'totalPagesAvailable' => $pages, 'sortOptions' => $this->getSortOptions(),
            'entityName' => getClassName($this->getNew()), 'order' => $order, 'limit' => $limit,
        ]];
    }

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
            ->where('(is_hidden = ? OR is_hidden IS NULL)', [0])
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
        $hidden = trim((string)($filters['is_hidden'] ?? ''));
        $color = trim((string)($filters['color'] ?? ''));
        $size = trim((string)($filters['size'] ?? ''));

        if ($search !== '') {
            $searchFields = ['title', 'sku', 'short_description', 'color', 'size', 'manufacturer'];

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

        if (in_array($hidden, ['0', '1'], true)) {
            $query->where('COALESCE(`is_hidden`, 0) = ?', [(int)$hidden]);
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
            ->where('group_id = ? AND (is_hidden = ? OR is_hidden IS NULL)', [(int)$groupId, 0])
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
            ->where('group_id IN (' . implode(',', array_fill(0, count($groupIds), '?')) . ') AND (is_hidden IS NULL OR is_hidden = ?)', array_merge($groupIds, [0]))
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
            if ((int)$product->getIsHidden() === 0) {
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
            ->where('`group_id` IN (' . implode(',', array_fill(0, count($groupIds), '?')) . ') AND `image` IS NOT NULL AND `image` != ? AND (is_hidden = ? OR is_hidden IS NULL)', array_merge($groupIds, ['', 0]))
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
