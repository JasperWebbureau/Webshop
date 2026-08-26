<?php

namespace Flexgrid\Modules\Webshop\Entity;

use Repository\ModuleEntity;

/**
 * @FG\Entity[name=WebshopInvoice,repository=Flexgrid\Modules\Webshop\Repository\WebshopInvoiceRepository,type=Webshop,in_menu=true,defaultOrder=id:DESC]
 */
class WebshopInvoice extends ModuleEntity
{
    /** @FG\Column[type=primary] */
    protected $id;

    /**
     * @FG\Column[type=varchar,role=title]
     * @FG\Filter::default[type=textsearch,html_title=Zoeken,fields={invoiceNumber,customerName,customerEmail}]
     * @FG\listForm[width=2]
     */
    protected $invoiceNumber;

    /** @FG\Column[type=int,parent=Flexgrid\Modules\Webshop\Entity\WebshopOrder::id] */
    protected $orderId;

    /** @FG\Column[type=int,label=Factuurdatum] */
    protected $invoiceDate;

    /** @FG\Column[type=varchar,label=Klantnaam] */
    protected $customerName;

    /** @FG\Column[type=email,label=E-mail] */
    protected $customerEmail;

    /** @FG\Column[type=text,label=Klant snapshot] */
    protected $customerSnapshot;

    /** @FG\Column[type=varchar,input_type=select,options={draft::Concept,issued::Uitgegeven,cancelled::Geannuleerd},label=Status] */
    protected $status;

    /** @FG\Column[type=varchar,label=Valuta] */
    protected $currency;

    /** @FG\Column[type=monetary,label=Subtotaal] */
    protected $subtotal;

    /** @FG\Column[type=monetary,label=BTW totaal] */
    protected $taxTotal;

    /** @FG\Column[type=monetary,label=Totaal] */
    protected $grandTotal;

    /** @FG\Column[type=file,label=PDF] */
    protected $pdfFile;

    public function getId() { return (int)$this->id; }
    public function setId($value) { $this->id = (int)$value; return $this; }
    public function getInvoiceNumber() { return (string)$this->invoiceNumber; }
    public function setInvoiceNumber($value) { $this->invoiceNumber = (string)$value; return $this; }
    public function getOrderId() { return (int)$this->orderId; }
    public function setOrderId($value) { $this->orderId = (int)$value; return $this; }
    public function getInvoiceDate() { return (int)$this->invoiceDate; }
    public function setInvoiceDate($value) { $this->invoiceDate = (int)$value; return $this; }
    public function getCustomerName() { return (string)$this->customerName; }
    public function setCustomerName($value) { $this->customerName = (string)$value; return $this; }
    public function getCustomerEmail() { return (string)$this->customerEmail; }
    public function setCustomerEmail($value) { $this->customerEmail = (string)$value; return $this; }
    public function getCustomerSnapshot() { return (string)$this->customerSnapshot; }
    public function setCustomerSnapshot($value) { $this->customerSnapshot = is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : (string)$value; return $this; }
    public function getStatus() { return (string)$this->status; }
    public function setStatus($value) { $this->status = (string)$value; return $this; }
    public function getCurrency() { return (string)($this->currency ?: 'EUR'); }
    public function setCurrency($value) { $this->currency = (string)$value; return $this; }
    public function getSubtotal() { return (float)$this->subtotal; }
    public function setSubtotal($value) { $this->subtotal = (float)$value; return $this; }
    public function getTaxTotal() { return (float)$this->taxTotal; }
    public function setTaxTotal($value) { $this->taxTotal = (float)$value; return $this; }
    public function getGrandTotal() { return (float)$this->grandTotal; }
    public function setGrandTotal($value) { $this->grandTotal = (float)$value; return $this; }
    public function getPdfFile() { return (string)$this->pdfFile; }
    public function setPdfFile($value) { $this->pdfFile = (string)$value; return $this; }


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



    // --- Auto-generated getters and setters ---

    /**
     * Get the children of WebshopInvoiceLine
     */
    public function getWebshopInvoiceLineChildren()
    {
        return (new \Flexgrid\Modules\Webshop\Repository\WebshopInvoiceLineRepository)->select()->where('`invoice_id` = ?',[ $this->getId()])->get();;
    }

}
