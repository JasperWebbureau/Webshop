<?php

namespace Flexgrid\Modules\Webshop\Service;

use Flexgrid\Media\MediaContext;
use Flexgrid\Media\MediaManager;
use Flexgrid\Modules\Webshop\Repository\WebshopProductGroupRepository;
use Flexgrid\Modules\Webshop\Repository\WebshopProductRepository;

class ProductGroupImageService
{
    public function cloneFirstProductImageForGroup(int $groupId, string $entityClass): int
    {
        return $this->cloneFirstProductImage([$groupId], $entityClass);
    }

    public function cloneFirstProductImageForMainGroup(int $mainGroupId, string $entityClass): int
    {
        $groupIds = (new WebshopProductGroupRepository())->getIdsByMainGroupId($mainGroupId);
        return $this->cloneFirstProductImage($groupIds, $entityClass);
    }

    private function cloneFirstProductImage(array $groupIds, string $entityClass): int
    {
        $product = (new WebshopProductRepository())->getFirstWithImageByGroupIds($groupIds);
        if ($product === null) {
            return 0;
        }

        $source = $product->getImage();
        $path = trim((string)$source->getPathFromRoot());
        if ($path === '') {
            return 0;
        }

        $media = (new MediaManager())->cloneLocalImage($path, new MediaContext($entityClass, 'image', 'local-image-clone'));
        return $media === null ? 0 : (int)$media->getId();
    }
}
