<?php

namespace Flexgrid\Modules\Webshop\Entity;

use Repository\ModuleEntity;

/**
 * @FG\Entity[name=WebshopOrder,repository=Flexgrid\Modules\Webshop\Repository\WebshopOrderRepository,type=Webshop,in_menu=true,defaultOrder=id:DESC]
 */
class WebshopOrder extends ModuleEntity
{
    /**
     * @FG\Column[type=primary]
     */
    protected $id;

    /**
     * @FG\Column[type=varchar,role=title]
     * @FG\Filter::default[type=textsearch,html_title=Zoeken,fields={orderNumber,customerEmail,customerName}]
     * @FG\listForm[width=2]
     */
    protected $orderNumber;

    /**
     * @FG\Column[type=int,parent=Flexgrid\Modules\Webshop\Entity\WebshopCustomer::id]
     * @FG\listForm[ignore=true]
     */
    protected $customerId;

    /**
     * @FG\Column[type=varchar,label=Klantnaam]
     * @FG\listForm[width=3]
     */
    protected $customerName;

    /**
     * @FG\Column[type=email,label=E-mail]
     * @FG\listForm[width=3]
     */
    protected $customerEmail;

    /**
     * @FG\Column[type=varchar,input_type=select,options={pending::In behandeling,confirmed::Bevestigd,shipped::Verzonden,cancelled::Geannuleerd,completed::Afgerond},label=Status]
     * @FG\Filter::default[type=checkboxMultipleSelect,html_title=Status]
     * @FG\listForm[width=2]
     */
    protected $status;

    /**
     * @FG\Column[type=varchar,input_type=select,options={unpaid::Niet betaald,paid::Betaald,failed::Mislukt,refunded::Terugbetaald},label=Betaalstatus]
     * @FG\listForm[width=2]
     */
    protected $paymentStatus;

    /**
     * @FG\Column[type=varchar,label=Valuta]
     * @FG\listForm[ignore=true]
     */
    protected $currency;

    /**
     * @FG\Column[type=monetary,label=Subtotaal]
     * @FG\listForm[ignore=true]
     */
    protected $subtotal;

    /**
     * @FG\Column[type=monetary,label=BTW totaal]
     * @FG\listForm[ignore=true]
     */
    protected $taxTotal;

    /**
     * @FG\Column[type=monetary,label=Verzending]
     * @FG\listForm[ignore=true]
     */
    protected $shippingTotal;

    /**
     * @FG\Column[type=float,label=BTW percentage verzending]
     * @FG\listForm[ignore=true]
     */
    protected $shippingTaxRate;

    /**
     * @FG\Column[type=monetary,label=BTW verzending]
     * @FG\listForm[ignore=true]
     */
    protected $shippingTaxTotal;

    /**
     * @FG\Column[type=int,parent=Flexgrid\Modules\Webshop\Entity\WebshopShippingMethod::id,label=Verzendmethode]
     * @FG\listForm[ignore=true]
     */
    protected $shippingMethodId;

    /**
     * @FG\Column[type=varchar,label=Verzendmethode titel]
     * @FG\listForm[ignore=true]
     */
    protected $shippingMethodTitle;

    /**
     * @FG\Column[type=varchar,label=Track & trace code]
     * @FG\listForm[ignore=true]
     */
    protected $shippingTrackingCode;

    /**
     * @FG\Column[type=varchar,label=Track & trace link]
     * @FG\listForm[ignore=true]
     */
    protected $shippingTrackingUrl;

    /**
     * @FG\Column[type=int,label=Verzonden op]
     * @FG\listForm[ignore=true]
     */
    protected $shippedAt;

    /**
     * @FG\Column[type=monetary,label=Korting]
     * @FG\listForm[ignore=true]
     */
    protected $discountTotal;

    /**
     * @FG\Column[type=int,parent=Flexgrid\Modules\Webshop\Entity\WebshopDiscountCode::id,label=Kortingscode]
     * @FG\listForm[ignore=true]
     */
    protected $discountCodeId;

    /**
     * @FG\Column[type=varchar,label=Kortingscode]
     * @FG\listForm[ignore=true]
     */
    protected $discountCode;

    /**
     * @FG\Column[type=monetary,label=Totaal]
     * @FG\listForm[width=2]
     */
    protected $grandTotal;

    /**
     * @FG\Column[type=text,label=Klant snapshot]
     * @FG\listForm[ignore=true]
     */
    protected $customerSnapshot;

    /**
     * @FG\Column[type=textarea,label=Opmerking]
     * @FG\listForm[ignore=true]
     */
    protected $customerNote;

    public function getId() { return (int)$this->id; }
    public function setId($value) { $this->id = (int)$value; return $this; }
    public function getOrderNumber() { return (string)$this->orderNumber; }
    public function setOrderNumber($value) { $this->orderNumber = (string)$value; return $this; }
    public function getCustomerId() { return (int)$this->customerId; }
    public function setCustomerId($value) { $this->customerId = (int)$value; return $this; }
    public function getCustomerName() { return (string)$this->customerName; }
    public function setCustomerName($value) { $this->customerName = (string)$value; return $this; }
    public function getCustomerEmail() { return (string)$this->customerEmail; }
    public function setCustomerEmail($value) { $this->customerEmail = (string)$value; return $this; }
    public function getStatus() { return (string)$this->status; }
    public function setStatus($value) { $this->status = (string)$value; return $this; }
    public function getPaymentStatus() { return (string)$this->paymentStatus; }
    public function setPaymentStatus($value) { $this->paymentStatus = (string)$value; return $this; }
    public function getCurrency() { return (string)($this->currency ?: 'EUR'); }
    public function setCurrency($value) { $this->currency = (string)$value; return $this; }
    public function getSubtotal() { return (float)$this->subtotal; }
    public function setSubtotal($value) { $this->subtotal = (float)$value; return $this; }
    public function getTaxTotal() { return (float)$this->taxTotal; }
    public function setTaxTotal($value) { $this->taxTotal = (float)$value; return $this; }
    public function getShippingTotal() { return (float)$this->shippingTotal; }
    public function setShippingTotal($value) { $this->shippingTotal = (float)$value; return $this; }
    public function getShippingTaxRate() { return (float)$this->shippingTaxRate; }
    public function setShippingTaxRate($value) { $this->shippingTaxRate = (float)$value; return $this; }
    public function getShippingTaxTotal() { return (float)$this->shippingTaxTotal; }
    public function setShippingTaxTotal($value) { $this->shippingTaxTotal = (float)$value; return $this; }
    public function getShippingMethodId() { return (int)$this->shippingMethodId; }
    public function setShippingMethodId($value) { $this->shippingMethodId = (int)$value; return $this; }
    public function getShippingMethodTitle() { return (string)$this->shippingMethodTitle; }
    public function setShippingMethodTitle($value) { $this->shippingMethodTitle = (string)$value; return $this; }
    public function getShippingTrackingCode() { return (string)$this->shippingTrackingCode; }
    public function setShippingTrackingCode($value) { $this->shippingTrackingCode = trim((string)$value); return $this; }
    public function getShippingTrackingUrl() { return (string)$this->shippingTrackingUrl; }
    public function setShippingTrackingUrl($value) { $this->shippingTrackingUrl = trim((string)$value); return $this; }
    public function getShippedAt() { return (int)$this->shippedAt; }
    public function setShippedAt($value) { $this->shippedAt = (int)$value; return $this; }
    public function getDiscountTotal() { return (float)$this->discountTotal; }
    public function setDiscountTotal($value) { $this->discountTotal = (float)$value; return $this; }
    public function getDiscountCodeId() { return (int)$this->discountCodeId; }
    public function setDiscountCodeId($value) { $this->discountCodeId = (int)$value; return $this; }
    public function getDiscountCode() { return (string)$this->discountCode; }
    public function setDiscountCode($value) { $this->discountCode = strtoupper(trim((string)$value)); return $this; }
    public function getGrandTotal() { return (float)$this->grandTotal; }
    public function setGrandTotal($value) { $this->grandTotal = (float)$value; return $this; }
    public function getCustomerSnapshot() { return (string)$this->customerSnapshot; }
    public function setCustomerSnapshot($value) { $this->customerSnapshot = is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : (string)$value; return $this; }
    public function getCustomerNote() { return (string)$this->customerNote; }
    public function setCustomerNote($value) { $this->customerNote = (string)$value; return $this; }


    // --- Auto-generated getters and setters ---

    /**
     * Get the parent entity for customerId.
     */
    public function getWebshopCustomerParent()
    {
        $id = $this->getCustomerId();
        if (empty($id)) {
            return null;
        }

        if (!isset($this->__autowireParentCache['customerIdParent']) ||
            $this->__autowireParentCache['customerIdParent']['id'] != $id) {
            $parent = (new \Flexgrid\Modules\Webshop\Repository\WebshopCustomerRepository())->select()->where('id = ?', [$id])->get()[0] ?? null;
            $this->__autowireParentCache['customerIdParent'] = [
                'id' => $id,
                'value' => $parent,
            ];
        }

        return $this->__autowireParentCache['customerIdParent']['value'];
    }

    /**
     * Get a display value for the parent entity of customerId.
     */
    public function getWebshopCustomerValue($getter = null)
    {
        $parent = $this->getWebshopCustomerParent();
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



    // --- Auto-generated getters and setters ---

    /**
     * Get the children of WebshopInvoice
     */
    public function getWebshopInvoiceChildren()
    {
        return (new \Flexgrid\Modules\Webshop\Repository\WebshopInvoiceRepository)->select()->where('`order_id` = ?',[ $this->getId()])->get();;
    }



    // --- Auto-generated getters and setters ---

    /**
     * Get the children of WebshopOrderLine
     */
    public function getWebshopOrderLineChildren()
    {
        return (new \Flexgrid\Modules\Webshop\Repository\WebshopOrderLineRepository)->select()->where('`order_id` = ?',[ $this->getId()])->get();;
    }



    // --- Auto-generated getters and setters ---

    /**
     * Get the children of WebshopPaymentTransaction
     */
    public function getWebshopPaymentTransactionChildren()
    {
        return (new \Flexgrid\Modules\Webshop\Repository\WebshopPaymentTransactionRepository)->select()->where('`order_id` = ?',[ $this->getId()])->get();;
    }



    // --- Auto-generated getters and setters ---

    /**
     * Get the parent entity for shippingMethodId.
     */
    public function getWebshopShippingMethodParent()
    {
        $id = $this->getShippingMethodId();
        if (empty($id)) {
            return null;
        }

        if (!isset($this->__autowireParentCache['shippingMethodIdParent']) ||
            $this->__autowireParentCache['shippingMethodIdParent']['id'] != $id) {
            $parent = (new \Flexgrid\Modules\Webshop\Repository\WebshopShippingMethodRepository())->select()->where('id = ?', [$id])->get()[0] ?? null;
            $this->__autowireParentCache['shippingMethodIdParent'] = [
                'id' => $id,
                'value' => $parent,
            ];
        }

        return $this->__autowireParentCache['shippingMethodIdParent']['value'];
    }

    /**
     * Get a display value for the parent entity of shippingMethodId.
     */
    public function getWebshopShippingMethodValue($getter = null)
    {
        $parent = $this->getWebshopShippingMethodParent();
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



    // --- Auto-generated getters and setters ---

    /**
     * Get the parent entity for discountCodeId.
     */
    public function getWebshopDiscountCodeParent()
    {
        $id = $this->getDiscountCodeId();
        if (empty($id)) {
            return null;
        }

        if (!isset($this->__autowireParentCache['discountCodeIdParent']) ||
            $this->__autowireParentCache['discountCodeIdParent']['id'] != $id) {
            $parent = (new \Flexgrid\Modules\Webshop\Repository\WebshopDiscountCodeRepository())->select()->where('id = ?', [$id])->get()[0] ?? null;
            $this->__autowireParentCache['discountCodeIdParent'] = [
                'id' => $id,
                'value' => $parent,
            ];
        }

        return $this->__autowireParentCache['discountCodeIdParent']['value'];
    }

    /**
     * Get a display value for the parent entity of discountCodeId.
     */
    public function getWebshopDiscountCodeValue($getter = null)
    {
        $parent = $this->getWebshopDiscountCodeParent();
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



    // --- Auto-generated getters and setters ---

    /**
     * Get the children of WebshopStockMutation
     */
    public function getWebshopStockMutationChildren()
    {
        return (new \Flexgrid\Modules\Webshop\Repository\WebshopStockMutationRepository)->select()->where('`order_id` = ?',[ $this->getId()])->get();;
    }

}
