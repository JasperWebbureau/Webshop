<?php
use Flexgrid\Html\Element\AjaxButton;
use Flexgrid\Modules\Webshop\Controller\WebshopAdminController;

$snapshot = json_decode((string)$invoice->getCustomerSnapshot(), true);
if (!is_array($snapshot)) {
    $snapshot = [];
}
?>
<section class="webshop-admin-invoice-detail">
    <grid>
        <div style="--cw:12;--cw-sm:12;--cw-xs:12">
            <div class="webshop-admin-invoices__header">
                <h1><?=htmlspecialchars((string)$invoice->getInvoiceNumber(), ENT_QUOTES, 'UTF-8')?></h1>
                <a class="button button-outline" href="<?=__DOMAIN__?>/Flexgrid/WebshopAdmin/invoices">
                    <i class="fas fa-arrow-left"></i> <?=t('webshop_admin_back_invoices', 'Terug naar facturen')?>
                </a>
            </div>
        </div>

        <div class="panel" style="--cw:4;--cw-sm:12;--cw-xs:12">
            <div class="panel__header">
                <h3><?=t('webshop_admin_invoice_customer', 'Klant')?></h3>
            </div>
            <div class="panel__body">
                <p><strong><?=htmlspecialchars((string)$invoice->getCustomerName(), ENT_QUOTES, 'UTF-8')?></strong></p>
                <p><?=htmlspecialchars((string)$invoice->getCustomerEmail(), ENT_QUOTES, 'UTF-8')?></p>
                <p>
                    <?=htmlspecialchars((string)($snapshot['address'] ?? ''), ENT_QUOTES, 'UTF-8')?><br>
                    <?=htmlspecialchars((string)($snapshot['postal_code'] ?? ''), ENT_QUOTES, 'UTF-8')?>
                    <?=htmlspecialchars((string)($snapshot['city'] ?? ''), ENT_QUOTES, 'UTF-8')?>
                </p>
            </div>
        </div>

        <div class="panel" style="--cw:4;--cw-sm:6;--cw-xs:12">
            <div class="panel__header">
                <h3><?=t('webshop_admin_invoice_meta', 'Factuurgegevens')?></h3>
            </div>
            <div class="panel__body">
                <p><?=t('webshop_admin_invoice_date', 'Datum')?>: <?=date('d-m-Y', (int)$invoice->getInvoiceDate())?></p>
                <p><?=t('webshop_admin_invoice_status', 'Status')?>: <?=htmlspecialchars((string)$invoice->getStatus(), ENT_QUOTES, 'UTF-8')?></p>
                <p><?=t('webshop_admin_invoice_order', 'Order')?>: <?=htmlspecialchars((string)$invoice->getOrderId(), ENT_QUOTES, 'UTF-8')?></p>
            </div>
        </div>

        <div class="panel" style="--cw:4;--cw-sm:6;--cw-xs:12">
            <div class="panel__header">
                <h3><?=t('webshop_admin_invoice_pdf', 'PDF')?></h3>
            </div>
            <div class="panel__body">
                <?php if (trim((string)$invoice->getPdfFile()) !== '') { ?>
                    <p><?=t('webshop_admin_invoice_pdf_ready', 'PDF is aangemaakt.')?></p>
                    <a class="button button-primary button-small" href="<?=__DOMAIN__?>/<?=htmlspecialchars(trim((string)$invoice->getPdfFile(), '/'), ENT_QUOTES, 'UTF-8')?>" target="_blank">
                        <?=t('webshop_admin_invoice_pdf_open', 'Open PDF')?>
                    </a>
                <?php } else { ?>
                    <p><?=t('webshop_admin_invoice_pdf_missing', 'Er is nog geen PDF aangemaakt.')?></p>
                <?php }
                $pdfButton = new AjaxButton(t('webshop_admin_invoice_pdf_generate', 'PDF genereren'));
                $pdfButton->setEvent(WebshopAdminController::class . '::generateInvoicePdf');
                $pdfButton->addClass('button button-outline button-small');
                $pdfButton->setAttribute('invoice_id', (int)$invoice->getId());
                $pdfButton->setAttribute('use-waiting-icon', 'true');
                echo $pdfButton;
                ?>
            </div>
        </div>

        <div class="panel" style="--cw:12;--cw-sm:12;--cw-xs:12">
            <div class="panel__header">
                <h3><?=t('webshop_admin_invoice_lines', 'Factuurregels')?></h3>
            </div>
            <div class="panel__body">
                <?php foreach ($lines as $line) { ?>
                    <div class="webshop-admin-invoice-detail__line">
                        <span><?=htmlspecialchars((string)$line->getTitle(), ENT_QUOTES, 'UTF-8')?> x <?=(int)$line->getQuantity()?></span>
                        <strong>&euro; <?=number_format((float)$line->getLineTotal(), 2, ',', '.')?></strong>
                    </div>
                <?php } ?>
                <div class="webshop-admin-invoice-detail__total">
                    <span><?=t('webshop_admin_invoice_tax_total', 'BTW')?></span>
                    <strong>&euro; <?=number_format((float)$invoice->getTaxTotal(), 2, ',', '.')?></strong>
                </div>
                <div class="webshop-admin-invoice-detail__total">
                    <span><?=t('webshop_admin_invoice_total', 'Totaal')?></span>
                    <strong>&euro; <?=number_format((float)$invoice->getGrandTotal(), 2, ',', '.')?></strong>
                </div>
            </div>
        </div>
    </grid>
</section>
