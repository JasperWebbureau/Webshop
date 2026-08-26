<?php
use Flexgrid\Event\AjaxEvent;
use Flexgrid\Html\Element\AjaxButton;
use Flexgrid\Modules\Webshop\Controller\WebshopController;
use Flexgrid\Modules\Webshop\Service\PriceService;

$priceService = new PriceService();
$updateEvent = new AjaxEvent(WebshopController::class, 'updateCartQuantity');
$updateEvent->setMinimumAccessLevel(0);
$checkoutUrl = '';
$productGridUrl = '';
$configurationErrors = [];
try {
    $checkoutUrl = WebshopController::getCheckoutUrl();
} catch (\Throwable $exception) {
    $configurationErrors[] = $exception->getMessage();
}
try {
    $productGridUrl = WebshopController::getProductGridUrl();
} catch (\Throwable $exception) {
    $configurationErrors[] = $exception->getMessage();
}
?>
<section class="webshop-cart-page">
    <grid>
        <div style="--cw:12;--cw-sm:12;--cw-xs:12">
            <div class="webshop-cart-page__header">
                <div class="webshop-cart-page__title">
                    <p><?=t('webshop_cart_eyebrow', 'Je winkelmand')?></p>
                    <h1><?=t('webshop_cart_title', 'Winkelwagen')?></h1>
                </div>
                <?php if (!empty($items)) {
                    $clearLabel = t('webshop_cart_clear', 'Winkelwagen legen');
                    $clearButton = new AjaxButton(t('webshop_cart_clear', 'Winkelwagen legen'));
                    $clearButton->setEvent(WebshopController::class . '::clearCart');
                    $clearButton->getEvent()->setMinimumAccessLevel(0);
                    $clearButton->addClass('button button-outline button-small webshop-cart-page__clear');
                    $clearButton->setAttribute('use-waiting-icon', 'true');
                    $clearButton->setHtml('<i class="fas fa-trash" aria-hidden="true"></i><span>' . htmlspecialchars($clearLabel, ENT_QUOTES, 'UTF-8') . '</span>');
                    echo $clearButton;
                } ?>
            </div>
        </div>

        <?php if (empty($items)) { ?>
            <div class="webshop-cart-page__empty" style="--cw:12;--cw-sm:12;--cw-xs:12">
                <i class="fas fa-shopping-cart"></i>
                <h2><?=t('webshop_cart_empty_title', 'Je winkelwagen is leeg')?></h2>
                <p><?=t('webshop_cart_empty', 'Je winkelwagen is leeg.')?></p>
                <?php if (!empty($configurationErrors)) { ?>
                    <div class="webshop-cart-page__configuration-error">
                        <?=htmlspecialchars(implode(' ', $configurationErrors), ENT_QUOTES, 'UTF-8')?>
                    </div>
                <?php } ?>
                <?php if (trim((string)$productGridUrl) !== '') { ?>
                    <a class="button button-primary" href="<?=$productGridUrl?>">
                        <?=t('webshop_cart_continue_shopping', 'Verder winkelen')?>
                    </a>
                <?php } ?>
            </div>
        <?php } else { ?>
            <div class="webshop-cart-page__items" style="--cw:8;--cw-sm:12;--cw-xs:12">
                <?php foreach ($items as $item) {
                    $product = $item['product'];
                    $removeButton = new AjaxButton('');
                    $removeButton->setEvent(WebshopController::class . '::removeFromCart');
                    $removeButton->getEvent()->setMinimumAccessLevel(0);
                    $removeButton->addClass('button button-transparent button-icon');
                    $removeButton->setAttribute('product_id', (int)$product->getId());
                    $removeButton->setAttribute('use-waiting-icon', 'true');
                    $removeButton->setAttribute('title', t('webshop_cart_remove_item', 'Product verwijderen'));
                    $removeButton->setAttribute('aria-label', t('webshop_cart_remove_item', 'Product verwijderen'));
                    $removeButton->setHtml('<i class="fas fa-trash"></i>');
                    ?>
                    <article class="webshop-cart-page__item">
                        <div class="webshop-cart-page__image">
                            <img src="<?=$product->getImage()->getResizeUrl(180, 180, 1)?>" alt="<?=htmlspecialchars((string)$product->getTitle(), ENT_QUOTES, 'UTF-8')?>" loading="lazy">
                        </div>
                        <div class="webshop-cart-page__info">
                            <h3>
                                <?=htmlspecialchars((string)$product->getTitle(), ENT_QUOTES, 'UTF-8')?>
                            </h3>
                            <?php if (trim((string)$product->getSku()) !== '') { ?>
                                <span><?=t('webshop_product_detail_sku', 'SKU')?>: <?=htmlspecialchars((string)$product->getSku(), ENT_QUOTES, 'UTF-8')?></span>
                            <?php } ?>
                        </div>
                        <div class="webshop-cart-page__quantity">
                            <form ajax="true" action="<?=$updateEvent->getName()?>" method="post">
                                <input type="hidden" name="product_id" value="<?=(int)$product->getId()?>">
                                <label>
                                    <span><?=t('webshop_cart_quantity', 'Aantal')?></span>
                                    <input
                                        type="number"
                                        min="0"
                                        name="quantity"
                                        value="<?=(int)$item['quantity']?>"
                                        onchange="$(this).closest('form').trigger('submit')"
                                    >
                                </label>
                            </form>
                        </div>
                        <div class="webshop-cart-page__price">
                            <small><?=t('webshop_cart_unit_price', 'Stukprijs')?> <?=$priceService->format((float)$item['unit_price'])?></small>
                            <span><?=$priceService->format((float)$item['line_total'])?></span>
                        </div>
                        <div class="webshop-cart-page__remove">
                            <?=$removeButton?>
                        </div>
                    </article>
                <?php } ?>
            </div>

            <aside class="webshop-cart-page__summary" style="--cw:4;--cw-sm:12;--cw-xs:12">
                <div class="panel">
                    <div class="panel__header">
                        <h3><?=t('webshop_cart_summary_title', 'Winkelwagen')?></h3>
                    </div>
                    <div class="panel__body">
                        <div class="webshop-cart-page__summary-line">
                            <span><?=t('webshop_cart_subtotal', 'Subtotaal')?></span>
                            <strong><?=$summary['subtotal_formatted'] ?? '&euro; 0,00'?></strong>
                        </div>
                        <div class="webshop-cart-page__summary-line">
                            <span><?=t('webshop_cart_items', 'Aantal artikelen')?></span>
                            <strong><?=(int)($summary['quantity'] ?? 0)?></strong>
                        </div>
                        <p class="webshop-cart-page__summary-note">
                            <?=t('webshop_cart_summary_note', 'Verzendkosten en eventuele korting worden berekend tijdens het afrekenen.')?>
                        </p>
                        <?php if (!empty($configurationErrors)) { ?>
                            <div class="webshop-cart-page__configuration-error">
                                <?=htmlspecialchars(implode(' ', $configurationErrors), ENT_QUOTES, 'UTF-8')?>
                            </div>
                        <?php } ?>
                        <?php if (trim((string)$checkoutUrl) !== '') { ?>
                            <a class="button button-primary button-wide" href="<?=$checkoutUrl?>">
                                <?=t('webshop_checkout_button', 'Afrekenen')?>
                            </a>
                        <?php } ?>
                        <?php if (trim((string)$productGridUrl) !== '') { ?>
                            <a class="button button-outline button-wide" href="<?=$productGridUrl?>">
                                <?=t('webshop_cart_continue_shopping', 'Verder winkelen')?>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </aside>
        <?php } ?>
    </grid>
</section>
