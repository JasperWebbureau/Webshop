<?php

namespace Flexgrid\Modules\Webshop\Entity;

use Repository\ModuleEntity;

/**
 * @FG\Entity[name=WebshopPaymentTransaction,repository=Flexgrid\Modules\Webshop\Repository\WebshopPaymentTransactionRepository,type=Webshop,in_menu=true,defaultOrder=id:DESC]
 */
class WebshopPaymentTransaction extends ModuleEntity
{
    /** @FG\Column[type=primary] */
    protected $id;

    /** @FG\Column[type=int,parent=Flexgrid\Modules\Webshop\Entity\WebshopOrder::id] */
    protected $orderId;

    /**
     * @FG\Column[type=varchar,role=title,label=Transactie referentie]
     * @FG\Filter::default[type=textsearch,html_title=Zoeken,fields={transactionReference,providerReference,customerEmail}]
     * @FG\listForm[width=3]
     */
    protected $transactionReference;

    /**
     * @FG\Column[type=varchar,input_type=select,options={manual::Handmatig,mollie::Mollie,stripe::Stripe},label=Provider]
     * @FG\listForm[width=2]
     */
    protected $provider;

    /** @FG\Column[type=varchar,label=Provider referentie] */
    protected $providerReference;

    /**
     * @FG\Column[type=varchar,input_type=select,options={pending::In afwachting,paid::Betaald,failed::Mislukt,cancelled::Geannuleerd,refunded::Terugbetaald},label=Status]
     * @FG\listForm[width=2]
     */
    protected $status;

    /** @FG\Column[type=monetary,label=Bedrag] */
    protected $amount;

    /** @FG\Column[type=varchar,label=Valuta] */
    protected $currency;

    /** @FG\Column[type=email,label=Klant e-mail] */
    protected $customerEmail;

    /** @FG\Column[type=int,label=Betaald op] */
    protected $paidAt;

    /** @FG\Column[type=text,label=Metadata] */
    protected $metadata;

    public function getId() { return (int)$this->id; }
    public function setId($value) { $this->id = (int)$value; return $this; }
    public function getOrderId() { return (int)$this->orderId; }
    public function setOrderId($value) { $this->orderId = (int)$value; return $this; }
    public function getTransactionReference() { return (string)$this->transactionReference; }
    public function setTransactionReference($value) { $this->transactionReference = (string)$value; return $this; }
    public function getProvider() { return (string)($this->provider ?: 'manual'); }
    public function setProvider($value) { $this->provider = (string)$value; return $this; }
    public function getProviderReference() { return (string)$this->providerReference; }
    public function setProviderReference($value) { $this->providerReference = (string)$value; return $this; }
    public function getStatus() { return (string)($this->status ?: 'pending'); }
    public function setStatus($value) { $this->status = (string)$value; return $this; }
    public function getAmount() { return (float)$this->amount; }
    public function setAmount($value) { $this->amount = (float)$value; return $this; }
    public function getCurrency() { return (string)($this->currency ?: 'EUR'); }
    public function setCurrency($value) { $this->currency = (string)$value; return $this; }
    public function getCustomerEmail() { return (string)$this->customerEmail; }
    public function setCustomerEmail($value) { $this->customerEmail = (string)$value; return $this; }
    public function getPaidAt() { return (int)$this->paidAt; }
    public function setPaidAt($value) { $this->paidAt = (int)$value; return $this; }
    public function getMetadata() { return (string)$this->metadata; }
    public function setMetadata($value) { $this->metadata = is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : (string)$value; return $this; }


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
