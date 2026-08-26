<section class="webshop-admin-invoices">
    <grid>
        <div style="--cw:12;--cw-sm:12;--cw-xs:12">
            <div class="webshop-admin-invoices__header">
                <h1><?=t('webshop_admin_invoices_overview_title', 'Facturen')?></h1>
                <a class="button button-outline" href="<?=__DOMAIN__?>/Flexgrid/WebshopAdmin/dashboard">
                    <i class="fas fa-arrow-left"></i> <?=t('webshop_admin_back_dashboard', 'Terug naar dashboard')?>
                </a>
            </div>
        </div>

        <div class="panel" style="--cw:12;--cw-sm:12;--cw-xs:12">
            <div class="panel__body">
                <div class="webshop-admin-invoices__table">
                    <?php foreach ($invoices as $invoice) { ?>
                        <article class="webshop-admin-invoices__row">
                            <div>
                                <strong><?=htmlspecialchars((string)$invoice->getInvoiceNumber(), ENT_QUOTES, 'UTF-8')?></strong>
                                <span><?=date('d-m-Y', (int)$invoice->getInvoiceDate())?></span>
                            </div>
                            <div><?=htmlspecialchars((string)$invoice->getCustomerName(), ENT_QUOTES, 'UTF-8')?></div>
                            <div><?=htmlspecialchars((string)$invoice->getCustomerEmail(), ENT_QUOTES, 'UTF-8')?></div>
                            <div>&euro; <?=number_format((float)$invoice->getGrandTotal(), 2, ',', '.')?></div>
                            <div><?=htmlspecialchars((string)$invoice->getStatus(), ENT_QUOTES, 'UTF-8')?></div>
                            <a class="button button-small" href="<?=__DOMAIN__?>/Flexgrid/WebshopAdmin/invoiceDetail/<?=(int)$invoice->getId()?>">
                                <?=t('webshop_admin_invoice_open', 'Open')?>
                            </a>
                        </article>
                    <?php } ?>
                    <?php if (empty($invoices)) { ?>
                        <p><?=t('webshop_admin_invoices_empty', 'Nog geen facturen.')?></p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </grid>
</section>
