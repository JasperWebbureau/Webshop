<?php
use Flexgrid\Event\AjaxEvent;
use Flexgrid\Html\Element\AjaxButton;
use Flexgrid\Modules\Webshop\Controller\WebshopAdminController;

$statusEvent = new AjaxEvent(WebshopAdminController::class, 'updateOrderStatus');
$paymentStatusEvent = new AjaxEvent(WebshopAdminController::class, 'updatePaymentStatus');
$shippingInfoEvent = new AjaxEvent(WebshopAdminController::class, 'updateShippingInfo');
$transactionStatusEvent = new AjaxEvent(WebshopAdminController::class, 'updatePaymentTransactionStatus');
$snapshot = json_decode((string)$order->getCustomerSnapshot(), true);
if (!is_array($snapshot)) {
    $snapshot = [];
}
?>
<section class="webshop-admin-order-detail">
    <grid>
        <div style="--cw:12;--cw-sm:12;--cw-xs:12">
            <div class="webshop-admin-orders__header">
                <h1><?=htmlspecialchars((string)$order->getOrderNumber(), ENT_QUOTES, 'UTF-8')?></h1>
                <a class="button button-outline" href="<?=__DOMAIN__?>/Flexgrid/WebshopAdmin/orders">
                    <i class="fas fa-arrow-left"></i> <?=t('webshop_admin_back_orders', 'Terug naar bestellingen')?>
                </a>
            </div>
        </div>

        <div class="panel" style="--cw:4;--cw-sm:12;--cw-xs:12">
            <div class="panel__header">
                <h3><?=t('webshop_admin_order_customer', 'Klant')?></h3>
            </div>
            <div class="panel__body">
                <p><strong><?=htmlspecialchars((string)$order->getCustomerName(), ENT_QUOTES, 'UTF-8')?></strong></p>
                <p><?=htmlspecialchars((string)$order->getCustomerEmail(), ENT_QUOTES, 'UTF-8')?></p>
                <p>
                    <?=htmlspecialchars((string)($snapshot['address'] ?? ''), ENT_QUOTES, 'UTF-8')?><br>
                    <?=htmlspecialchars((string)($snapshot['postal_code'] ?? ''), ENT_QUOTES, 'UTF-8')?>
                    <?=htmlspecialchars((string)($snapshot['city'] ?? ''), ENT_QUOTES, 'UTF-8')?>
                </p>
            </div>
        </div>

        <div class="panel" style="--cw:4;--cw-sm:6;--cw-xs:12">
            <div class="panel__header">
                <h3><?=t('webshop_admin_order_status', 'Status')?></h3>
            </div>
            <div class="panel__body">
                <select ajax="onchange" action="<?=$statusEvent->getName()?>" order_id="<?=(int)$order->getId()?>" name="status">
                    <?php foreach ($statusOptions as $value => $label) { ?>
                        <option value="<?=$value?>" <?=$order->getStatus() === $value ? 'selected' : ''?>><?=$label?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="panel" style="--cw:4;--cw-sm:6;--cw-xs:12">
            <div class="panel__header">
                <h3><?=t('webshop_admin_order_payment_status', 'Betaalstatus')?></h3>
            </div>
            <div class="panel__body">
                <select ajax="onchange" action="<?=$paymentStatusEvent->getName()?>" order_id="<?=(int)$order->getId()?>" name="payment_status">
                    <?php foreach ($paymentStatusOptions as $value => $label) { ?>
                        <option value="<?=$value?>" <?=$order->getPaymentStatus() === $value ? 'selected' : ''?>><?=$label?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="panel" style="--cw:4;--cw-sm:12;--cw-xs:12">
            <div class="panel__header">
                <h3><?=t('webshop_admin_order_shipping', 'Verzending')?></h3>
            </div>
            <div class="panel__body">
                <p><strong><?=htmlspecialchars((string)$order->getShippingMethodTitle(), ENT_QUOTES, 'UTF-8')?></strong></p>
                <p>&euro; <?=number_format((float)$order->getShippingTotal(), 2, ',', '.')?></p>
                <?php if ((float)$order->getShippingTaxTotal() > 0) { ?>
                    <p><?=t('webshop_admin_order_shipping_tax', 'BTW verzending')?>: &euro; <?=number_format((float)$order->getShippingTaxTotal(), 2, ',', '.')?> (<?=number_format((float)$order->getShippingTaxRate(), 2, ',', '.')?>%)</p>
                <?php } ?>
                <?php if ((int)$order->getShippedAt() > 0) { ?>
                    <p><?=t('webshop_admin_order_shipped_at', 'Verzonden op')?>: <?=date('d-m-Y H:i', (int)$order->getShippedAt())?></p>
                <?php } ?>
                <form class="webshop-admin-order-detail__shipping-form" ajax="true" action="<?=$shippingInfoEvent->getName()?>" method="post">
                    <input type="hidden" name="order_id" value="<?=(int)$order->getId()?>">
                    <input
                        type="text"
                        name="shipping_tracking_code"
                        value="<?=htmlspecialchars((string)$order->getShippingTrackingCode(), ENT_QUOTES, 'UTF-8')?>"
                        placeholder="<?=t('webshop_admin_order_tracking_code', 'Track & trace code')?>"
                    >
                    <input
                        type="url"
                        name="shipping_tracking_url"
                        value="<?=htmlspecialchars((string)$order->getShippingTrackingUrl(), ENT_QUOTES, 'UTF-8')?>"
                        placeholder="<?=t('webshop_admin_order_tracking_url', 'Track & trace link')?>"
                    >
                    <label class="webshop-admin-order-detail__checkbox">
                        <input type="checkbox" name="mark_shipped" value="1" <?=$order->getStatus() === 'shipped' ? 'checked' : ''?>>
                        <span><?=t('webshop_admin_order_mark_shipped', 'Markeer als verzonden')?></span>
                    </label>
                    <button class="button button-primary button-small" type="submit">
                        <?=t('webshop_admin_order_shipping_save', 'Opslaan')?>
                    </button>
                    <div data-webshop-shipping-feedback></div>
                </form>
            </div>
        </div>

        <div class="panel" style="--cw:4;--cw-sm:12;--cw-xs:12">
            <div class="panel__header">
                <h3><?=t('webshop_admin_order_discount', 'Korting')?></h3>
            </div>
            <div class="panel__body">
                <?php if ((float)$order->getDiscountTotal() > 0) { ?>
                    <p><strong><?=htmlspecialchars((string)$order->getDiscountCode(), ENT_QUOTES, 'UTF-8')?></strong></p>
                    <p>- &euro; <?=number_format((float)$order->getDiscountTotal(), 2, ',', '.')?></p>
                <?php } else { ?>
                    <p><?=t('webshop_admin_order_discount_empty', 'Geen korting toegepast.')?></p>
                <?php } ?>
            </div>
        </div>

        <div class="panel" style="--cw:4;--cw-sm:12;--cw-xs:12">
            <div class="panel__header">
                <h3><?=t('webshop_admin_order_invoice', 'Factuur')?></h3>
            </div>
            <div class="panel__body">
                <?php if ($invoice && (int)$invoice->getId() > 0) { ?>
                    <p><?=t('webshop_admin_order_invoice_exists', 'Factuur aangemaakt:')?> <strong><?=htmlspecialchars((string)$invoice->getInvoiceNumber(), ENT_QUOTES, 'UTF-8')?></strong></p>
                    <a class="button button-primary button-small" href="<?=__DOMAIN__?>/Flexgrid/WebshopAdmin/invoiceDetail/<?=(int)$invoice->getId()?>">
                        <?=t('webshop_admin_order_invoice_open', 'Open factuur')?>
                    </a>
                <?php } else {
                    $invoiceButton = new AjaxButton(t('webshop_admin_order_invoice_create', 'Factuur maken'));
                    $invoiceButton->setEvent(WebshopAdminController::class . '::createInvoice');
                    $invoiceButton->addClass('button button-primary button-small');
                    $invoiceButton->setAttribute('order_id', (int)$order->getId());
                    $invoiceButton->setAttribute('use-waiting-icon', 'true');
                    echo $invoiceButton;
                } ?>
            </div>
        </div>

        <div class="panel" style="--cw:12;--cw-sm:12;--cw-xs:12">
            <div class="panel__header">
                <h3><?=t('webshop_admin_order_lines', 'Orderregels')?></h3>
            </div>
            <div class="panel__body">
                <div class="webshop-admin-order-detail__lines">
                    <?php foreach ($lines as $line) { ?>
                        <div class="webshop-admin-order-detail__line">
                            <span>
                                <?=htmlspecialchars((string)$line->getProductTitle(), ENT_QUOTES, 'UTF-8')?>
                                x <?=(int)$line->getQuantity()?>
                            </span>
                            <strong>&euro; <?=number_format((float)$line->getLineTotal(), 2, ',', '.')?></strong>
                        </div>
                    <?php } ?>
                </div>
                <div class="webshop-admin-order-detail__line">
                    <span><?=t('webshop_admin_order_subtotal', 'Subtotaal')?></span>
                    <strong>&euro; <?=number_format((float)$order->getSubtotal(), 2, ',', '.')?></strong>
                </div>
                <?php if ((float)$order->getShippingTotal() > 0) { ?>
                    <div class="webshop-admin-order-detail__line">
                        <span><?=t('webshop_admin_order_shipping', 'Verzending')?></span>
                        <strong>&euro; <?=number_format((float)$order->getShippingTotal(), 2, ',', '.')?></strong>
                    </div>
                <?php } ?>
                <?php if ((float)$order->getDiscountTotal() > 0) { ?>
                    <div class="webshop-admin-order-detail__line">
                        <span><?=t('webshop_admin_order_discount', 'Korting')?></span>
                        <strong>- &euro; <?=number_format((float)$order->getDiscountTotal(), 2, ',', '.')?></strong>
                    </div>
                <?php } ?>
                <div class="webshop-admin-order-detail__line">
                    <span><?=t('webshop_admin_order_tax_total', 'BTW totaal')?></span>
                    <strong>&euro; <?=number_format((float)$order->getTaxTotal(), 2, ',', '.')?></strong>
                </div>
                <div class="webshop-admin-order-detail__total">
                    <span><?=t('webshop_admin_order_total', 'Totaal')?></span>
                    <strong>&euro; <?=number_format((float)$order->getGrandTotal(), 2, ',', '.')?></strong>
                </div>
            </div>
        </div>

        <div class="panel" style="--cw:12;--cw-sm:12;--cw-xs:12">
            <div class="panel__header">
                <h3><?=t('webshop_admin_order_payment_transactions', 'Betaaltransacties')?></h3>
            </div>
            <div class="panel__body">
                <div class="webshop-admin-order-detail__transactions">
                    <?php foreach ($paymentTransactions as $transaction) { ?>
                        <div class="webshop-admin-order-detail__transaction">
                            <div>
                                <strong><?=htmlspecialchars((string)$transaction->getTransactionReference(), ENT_QUOTES, 'UTF-8')?></strong>
                                <span><?=htmlspecialchars((string)$transaction->getProvider(), ENT_QUOTES, 'UTF-8')?> - &euro; <?=number_format((float)$transaction->getAmount(), 2, ',', '.')?></span>
                            </div>
                            <input
                                type="text"
                                ajax="onchange"
                                action="<?=$transactionStatusEvent->getName()?>"
                                transaction_id="<?=(int)$transaction->getId()?>"
                                status="<?=htmlspecialchars((string)$transaction->getStatus(), ENT_QUOTES, 'UTF-8')?>"
                                name="provider_reference"
                                value="<?=htmlspecialchars((string)$transaction->getProviderReference(), ENT_QUOTES, 'UTF-8')?>"
                                placeholder="<?=t('webshop_admin_payment_provider_reference', 'Provider referentie')?>"
                            >
                            <select ajax="onchange" action="<?=$transactionStatusEvent->getName()?>" transaction_id="<?=(int)$transaction->getId()?>" name="status">
                                <?php foreach ($transactionStatusOptions as $value => $label) { ?>
                                    <option value="<?=$value?>" <?=$transaction->getStatus() === $value ? 'selected' : ''?>><?=$label?></option>
                                <?php } ?>
                            </select>
                        </div>
                    <?php } ?>
                    <?php if (empty($paymentTransactions)) { ?>
                        <p><?=t('webshop_admin_order_payment_transactions_empty', 'Nog geen betaaltransacties.')?></p>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="panel" style="--cw:12;--cw-sm:12;--cw-xs:12">
            <div class="panel__header">
                <h3><?=t('webshop_admin_order_stock_mutations', 'Voorraadmutaties')?></h3>
            </div>
            <div class="panel__body">
                <div class="webshop-admin-order-detail__stock">
                    <?php foreach ($stockMutations as $mutation) { ?>
                        <div class="webshop-admin-order-detail__stock-row">
                            <span><?=t('webshop_admin_stock_product', 'Product')?> #<?=(int)$mutation->getProductId()?></span>
                            <span></span>
                            <strong><?=(int)$mutation->getQuantityChange()?></strong>
                            <span><?=(int)$mutation->getStockBefore()?> &rarr; <?=(int)$mutation->getStockAfter()?></span>
                        </div>
                    <?php } ?>
                    <?php if (empty($stockMutations)) { ?>
                        <p><?=t('webshop_admin_order_stock_mutations_empty', 'Geen voorraadmutaties voor deze order.')?></p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </grid>
</section>
