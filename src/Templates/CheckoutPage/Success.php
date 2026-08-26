<section class="webshop-checkout-page webshop-checkout-page--success">
    <grid>
        <div class="panel" style="--cw:12;--cw-sm:12;--cw-xs:12">
            <div class="panel__header">
                <h2><?=t('webshop_checkout_success_title', 'Bestelling ontvangen')?></h2>
            </div>
            <div class="panel__body">
                <p>
                    <?=t('webshop_checkout_success_text', 'Bedankt voor je bestelling. Je bestelnummer is:')?>
                    <strong><?=htmlspecialchars((string)$order->getOrderNumber(), ENT_QUOTES, 'UTF-8')?></strong>
                </p>
            </div>
        </div>
    </grid>
</section>
