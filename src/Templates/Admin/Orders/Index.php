<?php
use Flexgrid\Event\AjaxEvent;
use Flexgrid\Modules\Webshop\Controller\WebshopAdminController;

$statusEvent = new AjaxEvent(WebshopAdminController::class, 'updateOrderStatus');
$paymentStatusEvent = new AjaxEvent(WebshopAdminController::class, 'updatePaymentStatus');
?>
<section class="webshop-admin-orders">
    <grid>
        <div style="--cw:12;--cw-sm:12;--cw-xs:12">
            <div class="webshop-admin-orders__header">
                <h1><?=t('webshop_admin_orders_overview_title', 'Bestellingen')?></h1>
                <a class="button button-outline" href="<?=__DOMAIN__?>/Flexgrid/WebshopAdmin/dashboard">
                    <i class="fas fa-arrow-left"></i> <?=t('webshop_admin_back_dashboard', 'Terug naar dashboard')?>
                </a>
            </div>
        </div>

        <div class="panel" style="--cw:12;--cw-sm:12;--cw-xs:12">
            <div class="panel__body">
                <form class="webshop-admin-filters" method="get" action="<?=__DOMAIN__?>/Flexgrid/WebshopAdmin/orders">
                    <input
                        type="search"
                        name="q"
                        value="<?=htmlspecialchars((string)($filters['q'] ?? ''), ENT_QUOTES, 'UTF-8')?>"
                        placeholder="<?=t('webshop_admin_orders_search_placeholder', 'Zoek op bestelnummer, klant of e-mail')?>"
                    >
                    <select name="status">
                        <option value=""><?=t('webshop_admin_orders_all_statuses', 'Alle statussen')?></option>
                        <?php foreach ($statusOptions as $value => $label) { ?>
                            <option value="<?=$value?>" <?=($filters['status'] ?? '') === $value ? 'selected' : ''?>><?=$label?></option>
                        <?php } ?>
                    </select>
                    <select name="payment_status">
                        <option value=""><?=t('webshop_admin_orders_all_payment_statuses', 'Alle betaalstatussen')?></option>
                        <?php foreach ($paymentStatusOptions as $value => $label) { ?>
                            <option value="<?=$value?>" <?=($filters['payment_status'] ?? '') === $value ? 'selected' : ''?>><?=$label?></option>
                        <?php } ?>
                    </select>
                    <button class="button button-primary button-small" type="submit">
                        <?=t('webshop_admin_filter_apply', 'Filteren')?>
                    </button>
                    <?php if (trim((string)($filters['q'] ?? '')) !== '' || trim((string)($filters['status'] ?? '')) !== '' || trim((string)($filters['payment_status'] ?? '')) !== '') { ?>
                        <a class="button button-outline button-small" href="<?=__DOMAIN__?>/Flexgrid/WebshopAdmin/orders">
                            <?=t('webshop_admin_filter_reset', 'Reset')?>
                        </a>
                    <?php } ?>
                </form>
                <div class="webshop-admin-orders__table">
                    <?php foreach ($orders as $order) { ?>
                        <article class="webshop-admin-orders__row">
                            <div>
                                <strong><?=htmlspecialchars((string)$order->getOrderNumber(), ENT_QUOTES, 'UTF-8')?></strong>
                                <span><?=htmlspecialchars((string)$order->getCustomerName(), ENT_QUOTES, 'UTF-8')?></span>
                            </div>
                            <div><?=htmlspecialchars((string)$order->getCustomerEmail(), ENT_QUOTES, 'UTF-8')?></div>
                            <div>&euro; <?=number_format((float)$order->getGrandTotal(), 2, ',', '.')?></div>
                            <div>
                                <select ajax="onchange" action="<?=$statusEvent->getName()?>" order_id="<?=(int)$order->getId()?>" name="status">
                                    <?php foreach ($statusOptions as $value => $label) { ?>
                                        <option value="<?=$value?>" <?=$order->getStatus() === $value ? 'selected' : ''?>><?=$label?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div>
                                <select ajax="onchange" action="<?=$paymentStatusEvent->getName()?>" order_id="<?=(int)$order->getId()?>" name="payment_status">
                                    <?php foreach ($paymentStatusOptions as $value => $label) { ?>
                                        <option value="<?=$value?>" <?=$order->getPaymentStatus() === $value ? 'selected' : ''?>><?=$label?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <a class="button button-small" href="<?=__DOMAIN__?>/Flexgrid/WebshopAdmin/orderDetail/<?=(int)$order->getId()?>">
                                <?=t('webshop_admin_order_open', 'Open')?>
                            </a>
                        </article>
                    <?php } ?>
                    <?php if (empty($orders)) { ?>
                        <p><?=t('webshop_admin_orders_empty', 'Nog geen bestellingen.')?></p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </grid>
</section>
