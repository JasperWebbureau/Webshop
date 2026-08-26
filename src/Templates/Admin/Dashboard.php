<?php

?>

<div class="webshop-admin-dashboard">
    <section>
        <grid>
            <div class="panel" style="--cw:12;--cw-sm:12;--cw-xs:12">
                <div class="panel__header">
                    <h3><?=t('webshop_admin_dashboard_title', 'Webshop dashboard')?></h3>
                </div>
                <div class="panel__body">
                    <p><?=t('webshop_admin_dashboard_intro', 'Gebruik de webshopnavigatie om producten, bestellingen, klanten, facturen, rapportages en instellingen te beheren.')?></p>
                </div>
            </div>

            <div class="panel" style="--cw:3;--cw-sm:6;--cw-xs:12">
                <div class="panel__header">
                    <h4><?=t('webshop_admin_stat_orders', 'Orders')?></h4>
                </div>
                <div class="panel__body">
                    <strong class="webshop-admin-dashboard__stat"><?=$stats['order_count'] ?? 0?></strong>
                </div>
            </div>

            <div class="panel" style="--cw:3;--cw-sm:6;--cw-xs:12">
                <div class="panel__header">
                    <h4><?=t('webshop_admin_stat_pending', 'In behandeling')?></h4>
                </div>
                <div class="panel__body">
                    <strong class="webshop-admin-dashboard__stat"><?=$stats['pending_count'] ?? 0?></strong>
                </div>
            </div>

            <div class="panel" style="--cw:3;--cw-sm:6;--cw-xs:12">
                <div class="panel__header">
                    <h4><?=t('webshop_admin_stat_paid', 'Betaald')?></h4>
                </div>
                <div class="panel__body">
                    <strong class="webshop-admin-dashboard__stat"><?=$stats['paid_count'] ?? 0?></strong>
                </div>
            </div>

            <div class="panel" style="--cw:3;--cw-sm:6;--cw-xs:12">
                <div class="panel__header">
                    <h4><?=t('webshop_admin_stat_revenue', 'Omzet')?></h4>
                </div>
                <div class="panel__body">
                    <strong class="webshop-admin-dashboard__stat"><?=$stats['revenue_total_formatted'] ?? '&euro; 0,00'?></strong>
                </div>
            </div>

            <div class="panel" style="--cw:4;--cw-sm:6;--cw-xs:12">
                <div class="panel__header">
                    <h4><?=t('webshop_admin_orders_title', 'Bestellingen')?></h4>
                </div>
                <div class="panel__body">
                    <?php if (empty($recentOrders)) { ?>
                        <p><?=t('webshop_admin_orders_empty', 'Nog geen bestellingen.')?></p>
                    <?php } else { ?>
                        <?php foreach ($recentOrders as $order) { ?>
                            <a href="<?=__DOMAIN__?>/Flexgrid/WebshopAdmin/orderDetail/<?=(int)$order->getId()?>">
                                <i class="fas fa-receipt"></i>
                                <?=htmlspecialchars((string)$order->getOrderNumber(), ENT_QUOTES, 'UTF-8')?> -
                                <?=htmlspecialchars((string)$order->getCustomerName(), ENT_QUOTES, 'UTF-8')?>
                            </a><br>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>

            <div class="panel" style="--cw:4;--cw-sm:6;--cw-xs:12">
                <div class="panel__header">
                    <h4><?=t('webshop_admin_products_title', 'Producten')?></h4>
                </div>
                <div class="panel__body">
                    <a href="<?=__DOMAIN__?>/Flexgrid/WebshopAdmin/products">
                        <i class="fas fa-box"></i> <?=t('webshop_admin_products_open', 'Producten beheren')?>
                    </a>
                </div>
            </div>

            <div class="panel" style="--cw:4;--cw-sm:6;--cw-xs:12">
                <div class="panel__header">
                    <h4><?=t('webshop_admin_invoices_title', 'Facturen')?></h4>
                </div>
                <div class="panel__body">
                    <a href="<?=__DOMAIN__?>/Flexgrid/WebshopAdmin/invoices">
                        <i class="fas fa-file-invoice"></i> <?=t('webshop_admin_invoices_open', 'Facturen beheren')?>
                    </a>
                </div>
            </div>

            <div class="panel" style="--cw:4;--cw-sm:6;--cw-xs:12">
                <div class="panel__header">
                    <h4><?=t('webshop_admin_customers_title', 'Klanten')?></h4>
                </div>
                <div class="panel__body">
                    <a href="<?=__DOMAIN__?>/Flexgrid/WebshopAdmin/customers">
                        <i class="fas fa-users"></i> <?=t('webshop_admin_customers_open', 'Klanten bekijken')?>
                    </a>
                </div>
            </div>

            <div class="panel" style="--cw:4;--cw-sm:12;--cw-xs:12">
                <div class="panel__header">
                    <h4><?=t('webshop_admin_reports_title', 'Rapportages')?></h4>
                </div>
                <div class="panel__body">
                    <a href="<?=__DOMAIN__?>/Flexgrid/WebshopAdmin/reports">
                        <i class="fas fa-chart-line"></i> <?=t('webshop_admin_reports_open', 'Rapportages bekijken')?>
                    </a>
                </div>
            </div>
        </grid>
    </section>
</div>
