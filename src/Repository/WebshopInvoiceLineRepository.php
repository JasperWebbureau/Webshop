<?php

namespace Flexgrid\Modules\Webshop\Repository;

use Flexgrid\Modules\Webshop\Entity\WebshopInvoiceLine;
use Repository\Repository;

class WebshopInvoiceLineRepository extends Repository
{
    public function getEntity()
    {
        return new WebshopInvoiceLine();
    }

    public function getByInvoiceId(int $invoiceId): array
    {
        return $this->select(true)
            ->where('invoice_id = ?', [$invoiceId])
            ->get();
    }
}
