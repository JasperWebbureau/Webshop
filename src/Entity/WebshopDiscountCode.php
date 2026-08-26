<?php

namespace Flexgrid\Modules\Webshop\Entity;

use Repository\ModuleEntity;

/**
 * @FG\Entity[name=WebshopDiscountCode,repository=Flexgrid\Modules\Webshop\Repository\WebshopDiscountCodeRepository,type=Webshop,in_menu=true,defaultOrder=id:DESC]
 */
class WebshopDiscountCode extends ModuleEntity
{
    /** @FG\Column[type=primary] */
    protected $id;

    /**
     * @FG\Column[type=varchar,role=title,label=Code]
     * @FG\Filter::default[type=textsearch,html_title=Zoeken,fields={code,title}]
     * @FG\listForm[width=2]
     */
    protected $code;

    /** @FG\Column[type=varchar,label=Titel] */
    protected $title;

    /**
     * @FG\Column[type=varchar,input_type=select,options={fixed::Vast bedrag,percentage::Percentage},label=Type]
     * @FG\listForm[width=2]
     */
    protected $discountType;

    /**
     * @FG\Column[type=float,label=Waarde]
     * @FG\listForm[width=2]
     */
    protected $value;

    /** @FG\Column[type=monetary,label=Minimaal subtotaal] */
    protected $minimumSubtotal;

    /** @FG\Column[type=int,label=Actief] */
    protected $isActive;

    /** @FG\Column[type=int,label=Geldig vanaf] */
    protected $startsAt;

    /** @FG\Column[type=int,label=Geldig tot] */
    protected $expiresAt;

    /** @FG\Column[type=int,label=Maximaal gebruik] */
    protected $maxUses;

    /** @FG\Column[type=int,label=Gebruikt] */
    protected $usedCount;

    public function getId() { return (int)$this->id; }
    public function setId($value) { $this->id = (int)$value; return $this; }
    public function getCode() { return (string)$this->code; }
    public function setCode($value) { $this->code = strtoupper(trim((string)$value)); return $this; }
    public function getTitle() { return (string)$this->title; }
    public function setTitle($value) { $this->title = (string)$value; return $this; }
    public function getDiscountType() { return (string)($this->discountType ?: 'fixed'); }
    public function setDiscountType($value) { $this->discountType = (string)$value; return $this; }
    public function getValue() { return (float)$this->value; }
    public function setValue($value) { $this->value = (float)$value; return $this; }
    public function getMinimumSubtotal() { return (float)$this->minimumSubtotal; }
    public function setMinimumSubtotal($value) { $this->minimumSubtotal = (float)$value; return $this; }
    public function getIsActive() { return (int)$this->isActive; }
    public function setIsActive($value) { $this->isActive = (int)$value; return $this; }
    public function getStartsAt() { return (int)$this->startsAt; }
    public function setStartsAt($value) { $this->startsAt = (int)$value; return $this; }
    public function getExpiresAt() { return (int)$this->expiresAt; }
    public function setExpiresAt($value) { $this->expiresAt = (int)$value; return $this; }
    public function getMaxUses() { return (int)$this->maxUses; }
    public function setMaxUses($value) { $this->maxUses = (int)$value; return $this; }
    public function getUsedCount() { return (int)$this->usedCount; }
    public function setUsedCount($value) { $this->usedCount = (int)$value; return $this; }


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
        return (new \Flexgrid\Modules\Webshop\Repository\WebshopOrderRepository)->select()->where('`discount_code_id` = ?',[ $this->getId()])->get();;
    }

}
