<?php

namespace Flexgrid\Modules\Webshop\Entity;

use Repository\ModuleEntity;

/**
 * @FG\Entity[name=WebshopOrderLine,repository=Flexgrid\Modules\Webshop\Repository\WebshopOrderLineRepository,type=Webshop,hide=true]
 */
class WebshopOrderLine extends ModuleEntity
{
    /** @FG\Column[type=primary] */
    protected $id;

    /** @FG\Column[type=int,parent=Flexgrid\Modules\Webshop\Entity\WebshopOrder::id,role=group] */
    protected $orderId;

    /** @FG\Column[type=int,parent=Flexgrid\Modules\Webshop\Entity\WebshopProduct::id] */
    protected $productId;

    /** @FG\Column[type=varchar,role=title] */
    protected $productTitle;

    /** @FG\Column[type=varchar,label=SKU] */
    protected $sku;

    /** @FG\Column[type=int,label=Aantal] */
    protected $quantity;

    /** @FG\Column[type=monetary,label=Prijs per stuk] */
    protected $unitPrice;

    /** @FG\Column[type=float,label=BTW percentage] */
    protected $taxRate;

    /** @FG\Column[type=monetary,label=BTW bedrag] */
    protected $taxTotal;

    /** @FG\Column[type=monetary,label=Regeltotaal] */
    protected $lineTotal;

    public function getId() { return (int)$this->id; }
    public function setId($value) { $this->id = (int)$value; return $this; }
    public function getOrderId() { return (int)$this->orderId; }
    public function setOrderId($value) { $this->orderId = (int)$value; return $this; }
    public function getProductId() { return (int)$this->productId; }
    public function setProductId($value) { $this->productId = (int)$value; return $this; }
    public function getProductTitle() { return (string)$this->productTitle; }
    public function setProductTitle($value) { $this->productTitle = (string)$value; return $this; }
    public function getSku() { return (string)$this->sku; }
    public function setSku($value) { $this->sku = (string)$value; return $this; }
    public function getQuantity() { return (int)$this->quantity; }
    public function setQuantity($value) { $this->quantity = (int)$value; return $this; }
    public function getUnitPrice() { return (float)$this->unitPrice; }
    public function setUnitPrice($value) { $this->unitPrice = (float)$value; return $this; }
    public function getTaxRate() { return (float)$this->taxRate; }
    public function setTaxRate($value) { $this->taxRate = (float)$value; return $this; }
    public function getTaxTotal() { return (float)$this->taxTotal; }
    public function setTaxTotal($value) { $this->taxTotal = (float)$value; return $this; }
    public function getLineTotal() { return (float)$this->lineTotal; }
    public function setLineTotal($value) { $this->lineTotal = (float)$value; return $this; }


    // --- Auto-generated getters and setters ---

    /**
     * Get the parent entity for orderId.
     */
    public function getWebshopOrderParent()
    {
        $id = $this->getOrderId();
        if (empty($id)) {
            return null;
        }

        if (!isset($this->__autowireParentCache['orderIdParent']) ||
            $this->__autowireParentCache['orderIdParent']['id'] != $id) {
            $parent = (new \Flexgrid\Modules\Webshop\Repository\WebshopOrderRepository())->select()->where('id = ?', [$id])->get()[0] ?? null;
            $this->__autowireParentCache['orderIdParent'] = [
                'id' => $id,
                'value' => $parent,
            ];
        }

        return $this->__autowireParentCache['orderIdParent']['value'];
    }

    /**
     * Get a display value for the parent entity of orderId.
     */
    public function getWebshopOrderValue($getter = null)
    {
        $parent = $this->getWebshopOrderParent();
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

    /**
     * Get the parent entity for productId.
     */
    public function getWebshopProductParent()
    {
        $id = $this->getProductId();
        if (empty($id)) {
            return null;
        }

        if (!isset($this->__autowireParentCache['productIdParent']) ||
            $this->__autowireParentCache['productIdParent']['id'] != $id) {
            $parent = (new \Flexgrid\Modules\Webshop\Repository\WebshopProductRepository())->select()->where('id = ?', [$id])->get()[0] ?? null;
            $this->__autowireParentCache['productIdParent'] = [
                'id' => $id,
                'value' => $parent,
            ];
        }

        return $this->__autowireParentCache['productIdParent']['value'];
    }

    /**
     * Get a display value for the parent entity of productId.
     */
    public function getWebshopProductValue($getter = null)
    {
        $parent = $this->getWebshopProductParent();
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

    /**
     * Get the value of isHidden
     */
    public function getIsHidden()
    {
        return $this->isHidden;
    }

    /**
     * Set the value of isHidden
     *
     * @param mixed $value
     * @return $this
     */
    public function setIsHidden($value)
    {
        $this->isHidden = $value;
        return $this;
    }

    /**
     * Get the value of order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set the value of order
     *
     * @param mixed $value
     * @return $this
     */
    public function setOrder($value)
    {
        $this->order = $value;
        return $this;
    }

    /**
     * Get the value of makeTime
     */
    public function getMakeTime()
    {
        return $this->makeTime;
    }

    /**
     * Get the value of lastUpdate
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }

    /**
     * Set the value of lastUpdate
     *
     * @param mixed $value
     * @return $this
     */
    public function setLastUpdate($value)
    {
        $this->lastUpdate = $value;
        return $this;
    }

}
