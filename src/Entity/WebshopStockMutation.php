<?php

namespace Flexgrid\Modules\Webshop\Entity;

use Repository\ModuleEntity;

/**
 * @FG\Entity[name=WebshopStockMutation,repository=Flexgrid\Modules\Webshop\Repository\WebshopStockMutationRepository,type=Webshop,in_menu=true,defaultOrder=id:DESC]
 */
class WebshopStockMutation extends ModuleEntity
{
    /** @FG\Column[type=primary] */
    protected $id;

    /**
     * @FG\Column[type=int,parent=Flexgrid\Modules\Webshop\Entity\WebshopProduct::id]
     * @FG\listForm[width=2]
     */
    protected $productId;

    /**
     * @FG\Column[type=int,parent=Flexgrid\Modules\Webshop\Entity\WebshopOrder::id]
     * @FG\listForm[width=2]
     */
    protected $orderId;

    /**
     * @FG\Column[type=int,label=Mutatie]
     * @FG\listForm[width=1]
     */
    protected $quantityChange;

    /** @FG\Column[type=int,label=Voorraad voor] */
    protected $stockBefore;

    /** @FG\Column[type=int,label=Voorraad na] */
    protected $stockAfter;

    /**
     * @FG\Column[type=varchar,input_type=select,options={order::Order,manual::Handmatig,correction::Correctie,refund::Retour},label=Reden]
     * @FG\listForm[width=2]
     */
    protected $reason;

    /** @FG\Column[type=textarea,label=Notitie] */
    protected $note;

    public function getId() { return (int)$this->id; }
    public function setId($value) { $this->id = (int)$value; return $this; }
    public function getProductId() { return (int)$this->productId; }
    public function setProductId($value) { $this->productId = (int)$value; return $this; }
    public function getOrderId() { return (int)$this->orderId; }
    public function setOrderId($value) { $this->orderId = (int)$value; return $this; }
    public function getQuantityChange() { return (int)$this->quantityChange; }
    public function setQuantityChange($value) { $this->quantityChange = (int)$value; return $this; }
    public function getStockBefore() { return (int)$this->stockBefore; }
    public function setStockBefore($value) { $this->stockBefore = (int)$value; return $this; }
    public function getStockAfter() { return (int)$this->stockAfter; }
    public function setStockAfter($value) { $this->stockAfter = (int)$value; return $this; }
    public function getReason() { return (string)($this->reason ?: 'manual'); }
    public function setReason($value) { $this->reason = (string)$value; return $this; }
    public function getNote() { return (string)$this->note; }
    public function setNote($value) { $this->note = (string)$value; return $this; }


    // --- Auto-generated getters and setters ---

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
