<?php
?>
<section class="webshop-admin-customer-detail">
    <grid>
        <div style="--cw:12;--cw-sm:12;--cw-xs:12">
            <div class="webshop-admin-orders__header">
                <h1><?=htmlspecialchars((string)$customer->getTitle(), ENT_QUOTES, 'UTF-8')?></h1>
                <a class="button button-outline" href="<?=__DOMAIN__?>/Flexgrid/WebshopAdmin/customers">
                    <i class="fas fa-arrow-left"></i> <?=t('webshop_admin_back_customers', 'Terug naar klanten')?>
                </a>
            </div>
        </div>

        <div class="panel" style="--cw:4;--cw-sm:12;--cw-xs:12">
            <div class="panel__header">
                <h3><?=t('webshop_admin_customer_contact', 'Contact')?></h3>
            </div>
            <div class="panel__body">
                <p><strong><?=htmlspecialchars((string)$customer->getTitle(), ENT_QUOTES, 'UTF-8')?></strong></p>
                <?php if (trim((string)$customer->getCompanyName()) !== '') { ?>
                    <p><?=htmlspecialchars((string)$customer->getCompanyName(), ENT_QUOTES, 'UTF-8')?></p>
                <?php } ?>
                <p><?=htmlspecialchars((string)$customer->getEmail(), ENT_QUOTES, 'UTF-8')?></p>
                <p><?=htmlspecialchars((string)$customer->getPhone(), ENT_QUOTES, 'UTF-8')?></p>
            </div>
        </div>

        <div class="panel" style="--cw:4;--cw-sm:12;--cw-xs:12">
            <div class="panel__header">
                <h3><?=t('webshop_admin_customer_address', 'Adres')?></h3>
            </div>
            <div class="panel__body">
                <p><?=htmlspecialchars((string)$customer->getAddress(), ENT_QUOTES, 'UTF-8')?></p>
                <p>
                    <?=htmlspecialchars((string)$customer->getPostalCode(), ENT_QUOTES, 'UTF-8')?>
                    <?=htmlspecialchars((string)$customer->getCity(), ENT_QUOTES, 'UTF-8')?>
                </p>
                <p><?=htmlspecialchars((string)$customer->getCountry(), ENT_QUOTES, 'UTF-8')?></p>
            </div>
        </div>

        <div class="panel" style="--cw:4;--cw-sm:12;--cw-xs:12">
            <div class="panel__header">
                <h3><?=t('webshop_admin_customer_totals', 'Klantwaarde')?></h3>
            </div>
            <div class="panel__body">
                <p><?=t('webshop_admin_customer_order_count', 'Orders')?>: <strong><?=(int)($totals['order_count'] ?? 0)?></strong></p>
                <p><?=t('webshop_admin_customer_paid_count', 'Betaalde orders')?>: <strong><?=(int)($totals['paid_count'] ?? 0)?></strong></p>
                <p><?=t('webshop_admin_customer_revenue', 'Omzet')?>: <strong><?=$totals['revenue_total_formatted'] ?? '&euro; 0,00'?></strong></p>
                <a class="button button-outline button-small" href="<?=__DOMAIN__?>/Flexgrid/entityeditor/list/WebshopCustomer/<?=(int)$customer->getId()?>">
                    <?=t('webshop_admin_customer_edit', 'Klant bewerken')?>
                </a>
            </div>
        </div>

        <div class="panel" style="--cw:12;--cw-sm:12;--cw-xs:12">
            <div class="panel__header">
                <h3><?=t('webshop_admin_customer_orders', 'Bestellingen')?></h3>
            </div>
            <div class="panel__body">
                <div class="webshop-admin-orders__table">
                    <?php foreach ($orders as $order) { ?>
                        <article class="webshop-admin-orders__row">
                            <div>
                                <strong><?=htmlspecialchars((string)$order->getOrderNumber(), ENT_QUOTES, 'UTF-8')?></strong>
                                <span><?=htmlspecialchars((string)$order->getCustomerEmail(), ENT_QUOTES, 'UTF-8')?></span>
                            </div>
                            <div><?=htmlspecialchars((string)$order->getStatus(), ENT_QUOTES, 'UTF-8')?></div>
                            <div><?=htmlspecialchars((string)$order->getPaymentStatus(), ENT_QUOTES, 'UTF-8')?></div>
                            <div>&euro; <?=number_format((float)$order->getGrandTotal(), 2, ',', '.')?></div>
                            <div><?=date('d-m-Y H:i', (int)$order->getMakeTime())?></div>
                            <a class="button button-small" href="<?=__DOMAIN__?>/Flexgrid/WebshopAdmin/orderDetail/<?=(int)$order->getId()?>">
                                <?=t('webshop_admin_order_open', 'Open')?>
                            </a>
                        </article>
                    <?php } ?>
                    <?php if (empty($orders)) { ?>
                        <p><?=t('webshop_admin_customer_orders_empty', 'Deze klant heeft nog geen bestellingen.')?></p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </grid>
</section>
