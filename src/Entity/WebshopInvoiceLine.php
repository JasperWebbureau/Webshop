<?php

namespace Flexgrid\Modules\Webshop\Entity;

use Repository\ModuleEntity;

/**
 * @FG\Entity[name=WebshopInvoiceLine,repository=Flexgrid\Modules\Webshop\Repository\WebshopInvoiceLineRepository,type=Webshop,hide=true]
 */
class WebshopInvoiceLine extends ModuleEntity
{
    /** @FG\Column[type=primary] */
    protected $id;

    /** @FG\Column[type=int,parent=Flexgrid\Modules\Webshop\Entity\WebshopInvoice::id,role=group] */
    protected $invoiceId;

    /** @FG\Column[type=varchar,role=title] */
    protected $title;

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
    public function getInvoiceId() { return (int)$this->invoiceId; }
    public function setInvoiceId($value) { $this->invoiceId = (int)$value; return $this; }
    public function getTitle() { return (string)$this->title; }
    public function setTitle($value) { $this->title = (string)$value; return $this; }
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
     * Get the parent entity for invoiceId.
     */
    public function getWebshopInvoiceParent()
    {
        $id = $this->getInvoiceId();
        if (empty($id)) {
            return null;
        }

        if (!isset($this->__autowireParentCache['invoiceIdParent']) ||
            $this->__autowireParentCache['invoiceIdParent']['id'] != $id) {
            $parent = (new \Flexgrid\Modules\Webshop\Repository\WebshopInvoiceRepository())->select()->where('id = ?', [$id])->get()[0] ?? null;
            $this->__autowireParentCache['invoiceIdParent'] = [
                'id' => $id,
                'value' => $parent,
            ];
        }

        return $this->__autowireParentCache['invoiceIdParent']['value'];
    }

    /**
     * Get a display value for the parent entity of invoiceId.
     */
    public function getWebshopInvoiceValue($getter = null)
    {
        $parent = $this->getWebshopInvoiceParent();
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
