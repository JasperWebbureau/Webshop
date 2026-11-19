<?php
use Flexgrid\Modules\Webshop\Controller\WebshopController;

$cartPage = '';
$checkoutPage = '';
$configurationErrors = [];
try {
    $cartPage = WebshopController::getShoppingCartUrl();
} catch (\Throwable $exception) {
    $configurationErrors[] = $exception->getMessage();
}
try {
    $checkoutPage = WebshopController::getCheckoutUrl();
} catch (\Throwable $exception) {
    $configurationErrors[] = $exception->getMessage();
}
$items = $items ?? [];
$previewItems = array_slice($items, 0, 3);
?>

<div class="webshop-header-summary webshop-header-summary--cart webshop-cart-summary">
    <?=new \Flexgrid\Response\TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/HeaderSummary/HeaderSummaryTrigger.php', [
        'url' => $cartPage,
        'label' => t('webshop_cart_summary_open', 'Open winkelwagen'),
        'icon' => 'fas fa-shopping-cart',
        'quantity' => (int)($summary['quantity'] ?? 0),
        'countClass' => 'js-webshop-cart-count',
    ])?>

    <div class="webshop-header-summary__popover">
        <div class="webshop-header-summary__popover-header">
            <strong><?=t('webshop_cart_summary_title', 'Winkelwagen')?></strong>
            <span><?=$summary['subtotal_formatted'] ?? '&euro; 0,00'?></span>
        </div>

        <?php if (empty($previewItems)) { ?>
            <p class="webshop-header-summary__empty"><?=t('webshop_cart_summary_empty', 'Je winkelwagen is leeg.')?></p>
        <?php } else { ?>
            <div class="webshop-header-summary__items">
                <?php foreach ($previewItems as $item) {
                    $product = $item['product'];
                    ?>
                    <div class="webshop-header-summary__item">
                        <span class="webshop-header-summary__item-content">
                            <strong><?=htmlspecialchars((string)$product->getTitle(), ENT_QUOTES, 'UTF-8')?></strong>
                            <small><?=(int)$item['quantity']?> x &euro; <?=number_format((float)$item['unit_price'], 2, ',', '.')?></small>
                        </span>
                    </div>
                <?php } ?>
            </div>
            <?php if (count($items) > count($previewItems)) { ?>
                <div class="webshop-header-summary__more">
                    <?=t('webshop_cart_summary_more', 'Meer producten in je winkelwagen')?>
                </div>
            <?php } ?>
        <?php } ?>

        <?php if (!empty($configurationErrors)) { ?>
            <div class="webshop-header-summary__configuration-error">
                <?=htmlspecialchars(implode(' ', $configurationErrors), ENT_QUOTES, 'UTF-8')?>
            </div>
        <?php } ?>

        <div class="webshop-header-summary__actions">
            <?php if ($cartPage !== '') { ?>
                <a class="button button-outline button-primary button-small" href="<?=$cartPage?>">
                    <?=t('webshop_cart_summary_cart', 'Winkelwagen')?>
                </a>
            <?php } ?>
            <?php if ($checkoutPage !== '' && !empty($items)) { ?>
                <a class="button button-primary button-small" href="<?=$checkoutPage?>">
                    <?=t('webshop_cart_summary_checkout', 'Afrekenen')?>
                </a>
            <?php } ?>
        </div>
    </div>
</div>
