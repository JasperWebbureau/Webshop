<?php

namespace Flexgrid\Modules\Webshop\Entity;

use Repository\ModuleEntity;

/**
 * @FG\Entity[name=WebshopCustomer,repository=Flexgrid\Modules\Webshop\Repository\WebshopCustomerRepository,type=Webshop,in_menu=true,defaultOrder=id:DESC]
 */
class WebshopCustomer extends ModuleEntity
{
    /**
     * @FG\Column[type=primary]
     */
    protected $id;

    /**
     * @FG\Column[type=varchar,role=title]
     * @FG\Filter::default[type=textsearch,html_title=Zoeken,fields={title,email,companyName}]
     * @FG\listForm[width=3]
     */
    protected $title;

    /**
     * @FG\Column[type=email]
     * @FG\listForm[width=3]
     */
    protected $email;

    /**
     * @FG\Column[type=varchar,label=Bedrijfsnaam]
     * @FG\listForm[ignore=true]
     */
    protected $companyName;

    /**
     * @FG\Column[type=varchar,label=Telefoon]
     * @FG\listForm[ignore=true]
     */
    protected $phone;

    /**
     * @FG\Column[type=varchar,label=Adres]
     * @FG\listForm[ignore=true]
     */
    protected $address;

    /**
     * @FG\Column[type=varchar,label=Postcode]
     * @FG\listForm[ignore=true]
     */
    protected $postalCode;

    /**
     * @FG\Column[type=varchar,label=Plaats]
     * @FG\listForm[ignore=true]
     */
    protected $city;

    /**
     * @FG\Column[type=varchar,label=Land]
     * @FG\listForm[ignore=true]
     */
    protected $country;

    public function getId() { return (int)$this->id; }
    public function setId($value) { $this->id = (int)$value; return $this; }
    public function getTitle() { return (string)$this->title; }
    public function setTitle($value) { $this->title = (string)$value; return $this; }
    public function getEmail() { return (string)$this->email; }
    public function setEmail($value) { $this->email = (string)$value; return $this; }
    public function getCompanyName() { return (string)$this->companyName; }
    public function setCompanyName($value) { $this->companyName = (string)$value; return $this; }
    public function getPhone() { return (string)$this->phone; }
    public function setPhone($value) { $this->phone = (string)$value; return $this; }
    public function getAddress() { return (string)$this->address; }
    public function setAddress($value) { $this->address = (string)$value; return $this; }
    public function getPostalCode() { return (string)$this->postalCode; }
    public function setPostalCode($value) { $this->postalCode = (string)$value; return $this; }
    public function getCity() { return (string)$this->city; }
    public function setCity($value) { $this->city = (string)$value; return $this; }
    public function getCountry() { return (string)($this->country ?: 'NL'); }
    public function setCountry($value) { $this->country = (string)$value; return $this; }


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
        return (new \Flexgrid\Modules\Webshop\Repository\WebshopOrderRepository)->select()->where('`customer_id` = ?',[ $this->getId()])->get();;
    }

}
