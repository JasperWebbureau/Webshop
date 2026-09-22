<?php

namespace Flexgrid\Modules\Webshop\Entity;

use Flexgrid\Modules\Webshop\Repository\WebshopProductRepository;
use Repository\ModuleEntity;

/**
 * @FG\Entity[name=WebshopMoodboardItem,repository=Flexgrid\Modules\Webshop\Repository\WebshopMoodboardItemRepository,type=Webshop,in_menu=true,defaultOrder=order:ASC]
 */
class WebshopMoodboardItem extends ModuleEntity
{
    /**
     * @FG\Column[type=primary]
     * @FG\listForm[0=]
     */
    protected $id;

    /**
     * @FG\Column[type=int,parent=Flexgrid\Modules\Webshop\Entity\WebshopMoodboard::id,role=group,label=Moodboard]
     * @FG\listForm[ignore=true]
     */
    protected $moodboardId;

    /**
     * @FG\Column[type=int,parent=Flexgrid\Modules\Webshop\Entity\WebshopProduct::id,label=Product]
     * @FG\listForm[width=4]
     */
    protected $productId;

    /**
     * @FG\Column[type=float,label=X positie (%)]
     * @FG\listForm[width=2]
     */
    protected $positionX;

    /**
     * @FG\Column[type=float,label=Y positie (%)]
     * @FG\listForm[width=2]
     */
    protected $positionY;

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = (int)$value;
        return $this;
    }

    public function getMoodboardId()
    {
        return $this->moodboardId;
    }

    public function setMoodboardId($value)
    {
        $this->moodboardId = (int)$value;
        return $this;
    }

    public function getProductId()
    {
        return $this->productId;
    }

    public function setProductId($value)
    {
        $this->productId = (int)$value;
        return $this;
    }

    public function getPositionX()
    {
        return $this->positionX;
    }

    public function setPositionX($value)
    {
        $this->positionX = $this->normalizePercentage($value);
        return $this;
    }

    public function getPositionY()
    {
        return $this->positionY;
    }

    public function setPositionY($value)
    {
        $this->positionY = $this->normalizePercentage($value);
        return $this;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function setOrder($value)
    {
        $this->order = (int)$value;
        return $this;
    }

    public function getWebshopProductParent()
    {
        $id = (int)$this->getProductId();
        if ($id <= 0) {
            return null;
        }

        return (new WebshopProductRepository())->findById($id);
    }

    public function getWebshopProductValue($getter = null)
    {
        $product = $this->getWebshopProductParent();
        if (!$product || (int)$product->getId() <= 0) {
            return null;
        }

        if (!empty($getter) && method_exists($product, $getter)) {
            return $product->{$getter}();
        }

        return method_exists($product, 'getTitle') ? $product->getTitle() : $product;
    }

    protected function normalizePercentage($value): float
    {
        $number = (float)str_replace(',', '.', (string)$value);
        return max(0, min(100, $number));
    }


    // --- Auto-generated getters and setters ---

    /**
     * Get the parent entity for moodboardId.
     */
    public function getWebshopMoodboardParent()
    {
        $id = $this->getMoodboardId();
        if (empty($id)) {
            return null;
        }

        if (!isset($this->__autowireParentCache['moodboardIdParent']) ||
            $this->__autowireParentCache['moodboardIdParent']['id'] != $id) {
            $parent = (new \Flexgrid\Modules\Webshop\Repository\WebshopMoodboardRepository())->select()->where('id = ?', [$id])->get()[0] ?? null;
            $this->__autowireParentCache['moodboardIdParent'] = [
                'id' => $id,
                'value' => $parent,
            ];
        }

        return $this->__autowireParentCache['moodboardIdParent']['value'];
    }

    /**
     * Get a display value for the parent entity of moodboardId.
     */
    public function getWebshopMoodboardValue($getter = null)
    {
        $parent = $this->getWebshopMoodboardParent();
        if ($parent === null) {
            return null;
        }

        if (!empty($getter) && method_exists($parent, $getter)) {
            return $parent->{$getter}();
        }

        if (method_exists($parent, 'getTitle')) {
            return $parent->getTitle();
        }

        if (method_exists($parent, 'getName')) {
            return $parent->getName();
        }

        return $parent;
    }

}
