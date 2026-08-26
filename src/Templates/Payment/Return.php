<?php
use Flexgrid\Modules\Webshop\Controller\WebshopController;

$hasOrder = $order && (int)$order->getId() > 0;
$status = $transaction && (int)$transaction->getId() > 0 ? (string)$transaction->getStatus() : 'missing';
$isPaid = $status === 'paid';
$isPending = $status === 'pending';
$isFailed = !$isPaid && !$isPending;
$title = $isPaid
    ? t('webshop_payment_return_paid_title', 'Betaling ontvangen')
    : ($isPending ? t('webshop_payment_return_pending_title', 'Betaling in behandeling') : t('webshop_payment_return_failed_title', 'Betaling niet afgerond'));
$icon = $isPaid ? 'fas fa-check' : ($isPending ? 'fas fa-clock' : 'fas fa-exclamation-triangle');
$cartUrl = '';
$productGridUrl = '';
try {
    $cartUrl = WebshopController::getShoppingCartUrl();
} catch (\Throwable $exception) {
    $cartUrl = '';
}
try {
    $productGridUrl = WebshopController::getProductGridUrl();
} catch (\Throwable $exception) {
    $productGridUrl = '';
}
?>
<section class="webshop-payment-return webshop-payment-return--<?=htmlspecialchars($status, ENT_QUOTES, 'UTF-8')?>">
    <grid>
        <div class="webshop-payment-return__hero" style="--cw:12;--cw-sm:12;--cw-xs:12">
            <div class="webshop-payment-return__icon">
                <i class="<?=$icon?>"></i>
            </div>
            <h1><?=$title?></h1>
            <?php if ($isPaid) { ?>
                <p><?=t('webshop_payment_return_paid_text', 'Bedankt, je betaling is ontvangen. Je bestelling wordt nu verder verwerkt.')?></p>
            <?php } elseif ($isPending) { ?>
                <p><?=t('webshop_payment_return_pending_text', 'Zodra Mollie de betaling bevestigt, werken we de bestelling automatisch bij.')?></p>
            <?php } else { ?>
                <p><?=t('webshop_payment_return_failed_text', 'We konden geen afgeronde betaling vinden voor deze bestelling.')?></p>
            <?php } ?>
        </div>

        <div class="panel" style="--cw:7;--cw-sm:12;--cw-xs:12">
            <div class="panel__header">
                <h3><?=t('webshop_payment_return_details', 'Betaalgegevens')?></h3>
            </div>
            <div class="panel__body">
                <dl class="webshop-payment-return__details">
                    <?php if ($hasOrder) { ?>
                        <div>
                            <dt><?=t('webshop_payment_return_order', 'Bestelling')?></dt>
                            <dd><?=htmlspecialchars((string)$order->getOrderNumber(), ENT_QUOTES, 'UTF-8')?></dd>
                        </div>
                    <?php } ?>
                    <div>
                        <dt><?=t('webshop_payment_return_status', 'Status')?></dt>
                        <dd><?=htmlspecialchars($status, ENT_QUOTES, 'UTF-8')?></dd>
                    </div>
                    <?php if ($transaction && (int)$transaction->getId() > 0) { ?>
                        <div>
                            <dt><?=t('webshop_payment_return_provider', 'Betaalprovider')?></dt>
                            <dd><?=htmlspecialchars((string)$transaction->getProvider(), ENT_QUOTES, 'UTF-8')?></dd>
                        </div>
                        <div>
                            <dt><?=t('webshop_payment_return_reference', 'Referentie')?></dt>
                            <dd><?=htmlspecialchars((string)$transaction->getTransactionReference(), ENT_QUOTES, 'UTF-8')?></dd>
                        </div>
                        <div>
                            <dt><?=t('webshop_payment_return_amount', 'Bedrag')?></dt>
                            <dd><?=htmlspecialchars((string)$transaction->getCurrency(), ENT_QUOTES, 'UTF-8')?> <?=number_format((float)$transaction->getAmount(), 2, ',', '.')?></dd>
                        </div>
                    <?php } ?>
                    <?php if ($hasOrder) { ?>
                        <div>
                            <dt><?=t('webshop_payment_return_email', 'E-mail')?></dt>
                            <dd><?=htmlspecialchars((string)$order->getCustomerEmail(), ENT_QUOTES, 'UTF-8')?></dd>
                        </div>
                    <?php } ?>
                </dl>
            </div>
        </div>

        <aside class="panel" style="--cw:5;--cw-sm:12;--cw-xs:12">
            <div class="panel__header">
                <h3><?=t('webshop_payment_return_next', 'Vervolg')?></h3>
            </div>
            <div class="panel__body webshop-payment-return__actions">
                <?php if ($isPaid) { ?>
                    <p><?=t('webshop_payment_return_paid_next', 'Je ontvangt een bevestiging per e-mail zodra de bestelling is verwerkt.')?></p>
                <?php } elseif ($isPending) { ?>
                    <p><?=t('webshop_payment_return_pending_next', 'Je hoeft niets te doen. Vernieuw deze pagina later als de status nog niet is bijgewerkt.')?></p>
                <?php } else { ?>
                    <p><?=t('webshop_payment_return_failed_next', 'Je kunt de winkelwagen openen en opnieuw proberen af te rekenen.')?></p>
                <?php } ?>
                <?php if ($isFailed && $cartUrl !== '') { ?>
                    <a class="button button-primary button-wide" href="<?=$cartUrl?>">
                        <?=t('webshop_payment_return_back_cart', 'Terug naar winkelwagen')?>
                    </a>
                <?php } ?>
                <?php if ($productGridUrl !== '') { ?>
                    <a class="button button-outline button-wide" href="<?=$productGridUrl?>">
                        <?=t('webshop_payment_return_continue_shopping', 'Verder winkelen')?>
                    </a>
                <?php } ?>
            </div>
        </aside>
    </grid>
</section>
