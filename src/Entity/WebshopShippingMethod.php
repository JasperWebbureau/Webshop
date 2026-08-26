<?php

namespace Flexgrid\Modules\Webshop\Entity;

use Repository\ModuleEntity;

/**
 * @FG\Entity[name=WebshopShippingMethod,repository=Flexgrid\Modules\Webshop\Repository\WebshopShippingMethodRepository,type=Webshop,in_menu=true,defaultOrder=order:ASC]
 */
class WebshopShippingMethod extends ModuleEntity
{
    /** @FG\Column[type=primary] */
    protected $id;

    /**
     * @FG\Column[type=varchar,role=title,label=Titel]
     * @FG\Filter::default[type=textsearch,html_title=Zoeken,fields={title,description}]
     * @FG\listForm[width=4]
     */
    protected $title;

    /** @FG\Column[type=textarea,label=Omschrijving] */
    protected $description;

    /**
     * @FG\Column[type=monetary,label=Prijs]
     * @FG\listForm[width=2]
     */
    protected $price;

    /** @FG\Column[type=int,parent=Flexgrid\Modules\Webshop\Entity\WebshopTaxRate::id,label=BTW tarief] */
    protected $taxRateId;

    /**
     * @FG\Column[type=int,label=Actief]
     * @FG\listForm[width=1]
     */
    protected $isActive;

    /** @FG\Column[type=int,label=Gratis vanaf] */
    protected $freeFrom;

    public function getId() { return (int)$this->id; }
    public function setId($value) { $this->id = (int)$value; return $this; }
    public function getTitle() { return (string)$this->title; }
    public function setTitle($value) { $this->title = (string)$value; return $this; }
    public function getDescription() { return (string)$this->description; }
    public function setDescription($value) { $this->description = (string)$value; return $this; }
    public function getPrice() { return (float)$this->price; }
    public function setPrice($value) { $this->price = (float)$value; return $this; }
    public function getTaxRateId() { return (int)$this->taxRateId; }
    public function setTaxRateId($value) { $this->taxRateId = (int)$value; return $this; }
    public function getIsActive() { return (int)$this->isActive; }
    public function setIsActive($value) { $this->isActive = (int)$value; return $this; }
    public function getFreeFrom() { return (float)$this->freeFrom; }
    public function setFreeFrom($value) { $this->freeFrom = (float)$value; return $this; }


    // --- Auto-generated getters and setters ---

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



    // --- Auto-generated getters and setters ---

    /**
     * Get the children of WebshopOrder
     */
    public function getWebshopOrderChildren()
    {
        return (new \Flexgrid\Modules\Webshop\Repository\WebshopOrderRepository)->select()->where('`shipping_method_id` = ?',[ $this->getId()])->get();;
    }



    // --- Auto-generated getters and setters ---

    /**
     * Get the parent entity for taxRateId.
     */
    public function getWebshopTaxRateParent()
    {
        $id = $this->getTaxRateId();
        if (empty($id)) {
            return null;
        }

        if (!isset($this->__autowireParentCache['taxRateIdParent']) ||
            $this->__autowireParentCache['taxRateIdParent']['id'] != $id) {
            $parent = (new \Flexgrid\Modules\Webshop\Repository\WebshopTaxRateRepository())->select()->where('id = ?', [$id])->get()[0] ?? null;
            $this->__autowireParentCache['taxRateIdParent'] = [
                'id' => $id,
                'value' => $parent,
            ];
        }

        return $this->__autowireParentCache['taxRateIdParent']['value'];
    }

    /**
     * Get a display value for the parent entity of taxRateId.
     */
    public function getWebshopTaxRateValue($getter = null)
    {
        $parent = $this->getWebshopTaxRateParent();
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
