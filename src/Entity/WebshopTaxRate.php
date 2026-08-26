<?php

namespace Flexgrid\Modules\Webshop\Entity;

use Repository\ModuleEntity;

/**
 * @FG\Entity[name=WebshopTaxRate,repository=Flexgrid\Modules\Webshop\Repository\WebshopTaxRateRepository,type=Webshop,in_menu=true,defaultOrder=rate:DESC]
 */
class WebshopTaxRate extends ModuleEntity
{
    /** @FG\Column[type=primary] */
    protected $id;

    /**
     * @FG\Column[type=varchar,role=title,label=Titel]
     * @FG\Filter::default[type=textsearch,html_title=Zoeken,fields={title}]
     * @FG\listForm[width=4]
     */
    protected $title;

    /**
     * @FG\Column[type=float,label=Percentage]
     * @FG\listForm[width=2]
     */
    protected $rate;

    /**
     * @FG\Column[type=tinyint,label=Actief]
     * @FG\listForm[width=1]
     */
    protected $isActive;

    /**
     * @FG\Column[type=tinyint,label=Standaard]
     * @FG\listForm[width=1]
     */
    protected $isDefault;

    public function getId() { return (int)$this->id; }
    public function setId($value) { $this->id = (int)$value; return $this; }
    public function getTitle() { return (string)$this->title; }
    public function setTitle($value) { $this->title = (string)$value; return $this; }
    public function getRate() { return (float)$this->rate; }
    public function setRate($value) { $this->rate = (float)$value; return $this; }
    public function getIsActive() { return (int)$this->isActive; }
    public function setIsActive($value) { $this->isActive = (int)$value; return $this; }
    public function getIsDefault() { return (int)$this->isDefault; }
    public function setIsDefault($value) { $this->isDefault = (int)$value; return $this; }


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
     * Get the children of WebshopProduct
     */
    public function getWebshopProductChildren()
    {
        return (new \Flexgrid\Modules\Webshop\Repository\WebshopProductRepository)->select()->where('`tax_rate_id` = ?',[ $this->getId()])->get();;
    }



    // --- Auto-generated getters and setters ---

    /**
     * Get the children of WebshopShippingMethod
     */
    public function getWebshopShippingMethodChildren()
    {
        return (new \Flexgrid\Modules\Webshop\Repository\WebshopShippingMethodRepository)->select()->where('`tax_rate_id` = ?',[ $this->getId()])->get();;
    }

}
