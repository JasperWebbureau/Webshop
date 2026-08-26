<section class="webshop-admin-reports">
    <grid>
        <div style="--cw:12;--cw-sm:12;--cw-xs:12">
            <div class="webshop-admin-reports__header">
                <h1><?=t('webshop_admin_reports_overview_title', 'Rapportages')?></h1>
                <a class="button button-outline" href="<?=__DOMAIN__?>/Flexgrid/WebshopAdmin/dashboard">
                    <i class="fas fa-arrow-left"></i> <?=t('webshop_admin_back_dashboard', 'Terug naar dashboard')?>
                </a>
            </div>
        </div>

        <div class="panel" style="--cw:6;--cw-sm:6;--cw-xs:12">
            <div class="panel__header">
                <h3><?=t('webshop_admin_reports_orders_summary', 'Betaalde orders')?></h3>
            </div>
            <div class="panel__body">
                <strong class="webshop-admin-reports__stat"><?=$orderSummary['revenue_formatted']?></strong>
                <span><?=t('webshop_admin_reports_orders_count', 'Aantal')?>: <?=(int)$orderSummary['paid_count']?></span>
                <span><?=t('webshop_admin_reports_tax', 'BTW')?>: <?=$orderSummary['tax_formatted']?></span>
            </div>
        </div>

        <div class="panel" style="--cw:6;--cw-sm:6;--cw-xs:12">
            <div class="panel__header">
                <h3><?=t('webshop_admin_reports_invoices_summary', 'Facturen')?></h3>
            </div>
            <div class="panel__body">
                <strong class="webshop-admin-reports__stat"><?=$invoiceSummary['revenue_formatted']?></strong>
                <span><?=t('webshop_admin_reports_invoices_count', 'Aantal')?>: <?=(int)$invoiceSummary['paid_count']?></span>
                <span><?=t('webshop_admin_reports_tax', 'BTW')?>: <?=$invoiceSummary['tax_formatted']?></span>
            </div>
        </div>

        <div class="panel" style="--cw:12;--cw-sm:12;--cw-xs:12">
            <div class="panel__header">
                <h3><?=t('webshop_admin_reports_monthly', 'Per maand')?></h3>
            </div>
            <div class="panel__body">
                <div class="webshop-admin-reports__table">
                    <div class="webshop-admin-reports__row webshop-admin-reports__row--head">
                        <span><?=t('webshop_admin_reports_period', 'Periode')?></span>
                        <span><?=t('webshop_admin_reports_orders', 'Orders')?></span>
                        <span><?=t('webshop_admin_reports_order_revenue', 'Orderomzet')?></span>
                        <span><?=t('webshop_admin_reports_invoices', 'Facturen')?></span>
                        <span><?=t('webshop_admin_reports_invoice_revenue', 'Factuuromzet')?></span>
                    </div>
                    <?php foreach ($monthly as $row) { ?>
                        <div class="webshop-admin-reports__row">
                            <strong><?=htmlspecialchars((string)$row['label'], ENT_QUOTES, 'UTF-8')?></strong>
                            <span><?=(int)$row['orders']?></span>
                            <span><?=$row['order_revenue_formatted']?></span>
                            <span><?=(int)$row['invoices']?></span>
                            <span><?=$row['invoice_revenue_formatted']?></span>
                        </div>
                    <?php } ?>
                    <?php if (empty($monthly)) { ?>
                        <p><?=t('webshop_admin_reports_empty', 'Nog geen rapportagedata.')?></p>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="panel" style="--cw:12;--cw-sm:12;--cw-xs:12">
            <div class="panel__header">
                <h3><?=t('webshop_admin_reports_yearly', 'Per jaar')?></h3>
            </div>
            <div class="panel__body">
                <div class="webshop-admin-reports__table">
                    <div class="webshop-admin-reports__row webshop-admin-reports__row--head">
                        <span><?=t('webshop_admin_reports_period', 'Periode')?></span>
                        <span><?=t('webshop_admin_reports_orders', 'Orders')?></span>
                        <span><?=t('webshop_admin_reports_order_revenue', 'Orderomzet')?></span>
                        <span><?=t('webshop_admin_reports_invoices', 'Facturen')?></span>
                        <span><?=t('webshop_admin_reports_invoice_revenue', 'Factuuromzet')?></span>
                    </div>
                    <?php foreach ($yearly as $row) { ?>
                        <div class="webshop-admin-reports__row">
                            <strong><?=htmlspecialchars((string)$row['label'], ENT_QUOTES, 'UTF-8')?></strong>
                            <span><?=(int)$row['orders']?></span>
                            <span><?=$row['order_revenue_formatted']?></span>
                            <span><?=(int)$row['invoices']?></span>
                            <span><?=$row['invoice_revenue_formatted']?></span>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </grid>
</section>
