<?php
/**
 * @var $entity \Flexgrid\Modules\Webshop\Entity\WebshopProduct
 */

use Flexgrid\Utils\_Color;

$product = $entity;
$detailUrl = $product->getDetailUrl($pageId);
$title = htmlspecialchars((string)$product->getTitle(), ENT_QUOTES, 'UTF-8');
$groupTitle = trim((string)$product->getWebshopProductGroupValue());
$price = (float)$product->getPrice();
$salePrice = (float)$product->getSalePrice();
$hasSale = $salePrice > 0 && $salePrice < $price;
$activePrice = $hasSale ? $salePrice : $price;
$isInStock = (int)$product->getTrackStock() !== 1 || (int)$product->getStock() > 0;
$variantProducts = method_exists($product, 'getVariantClusterProducts') ? $product->getVariantClusterProducts(true) : [$product];
$getColorCssValue = static function ($color) {
    return _Color::getHex($color, '#c8b79e');
};
$variantColors = [];
$variantSizes = [];

foreach ($variantProducts as $variantProduct) {
    $variantColor = trim((string)$variantProduct->getColor());
    $variantSize = trim((string)$variantProduct->getSize());

    if ($variantColor !== '') {
        $variantColors[$variantColor] = true;
    }

    if ($variantSize !== '') {
        $variantSizes[$variantSize] = true;
    }
}

$optionMode = count($variantColors) > 1 ? 'color' : (count($variantSizes) > 1 ? 'size' : 'title');
$variantOptions = [];

foreach ($variantProducts as $variantProduct) {
    $optionKey = (string)$variantProduct->getId();

    if ($optionMode === 'color' && trim((string)$variantProduct->getColor()) !== '') {
        $optionKey = strtolower(trim((string)$variantProduct->getColor()));
    } elseif ($optionMode === 'size' && trim((string)$variantProduct->getSize()) !== '') {
        $optionKey = strtolower(trim((string)$variantProduct->getSize()));
    }

    if (!isset($variantOptions[$optionKey]) || (int)$variantProduct->getId() === (int)$product->getId()) {
        $variantOptions[$optionKey] = $variantProduct;
    }
}

$variantOptions = array_values($variantOptions);

if (count($variantProducts) > 1) {
    $variantProductPrices = [];
    foreach ($variantProducts as $variantProduct) {
        $variantProductPrice = (float)$variantProduct->getPrice();
        $variantProductSalePrice = (float)$variantProduct->getSalePrice();
        $variantProductPrices[] = $variantProductSalePrice > 0 && $variantProductSalePrice < $variantProductPrice ? $variantProductSalePrice : $variantProductPrice;
    }

    if (!empty($variantProductPrices)) {
        $activePrice = min($variantProductPrices);
    }
}

$makeTime = method_exists($product, 'getMakeTime') ? strtotime((string)$product->getMakeTime()) : false;
$isNew = !$hasSale && $makeTime !== false && $makeTime >= strtotime('-30 days');
$badgeText = $hasSale ? t('webshop_product_card_sale', 'AANBIEDING') : ($isNew ? t('webshop_product_card_new', 'NIEUW') : '');
$badgeClass = $hasSale ? 'is-sale' : ($isNew ? 'is-new' : '');
$pricePrefix = count($variantProducts) > 1 ? t('webshop_product_card_from', 'Vanaf') . ' ' : '';
?>
<article class="webshop-product-card2 clickable" style="--cw:<?=$cardWidth?>;--cw-sm:6;--cw-xs:12">
    <div class="webshop-product-card2__media">
        <a class="webshop-product-card2__image-link" href="<?=$detailUrl?>" aria-label="<?=$title?>">
            <img
                src="<?=$product->getImage()->getResizeUrl(820, 660, 1)?>"
                alt="<?=$title?>"
                loading="lazy"
            >
        </a>
        <?php if ($badgeText !== '') { ?>
            <span class="webshop-product-card2__badge <?=$badgeClass?>"><?=$badgeText?></span>
        <?php } ?>
        <a class="webshop-product-card2__favorite" href="<?=$detailUrl?>" aria-label="<?=$title?>">
            <i class="far fa-heart" aria-hidden="true"></i>
        </a>
    </div>

    <div class="webshop-product-card2__content">
        <?php if ($groupTitle !== '') { ?>
            <div class="webshop-product-card2__category"><?=htmlspecialchars($groupTitle, ENT_QUOTES, 'UTF-8')?></div>
        <?php } ?>

        <h3 class="webshop-product-card2__title">
           <?=$title?>
        </h3>

        <div class="webshop-product-card2__price">
            <?php if ($hasSale) { ?>
                <span class="webshop-product-card2__price-old">&euro; <?=number_format($price, 2, ',', '.')?></span>
            <?php } ?>
            <span class="webshop-product-card2__price-current"><?=$pricePrefix?>&euro; <?=number_format($activePrice, 2, ',', '.')?></span>
        </div>
    </div>

    <div class="webshop-product-card2__options">
        <?php if (count($variantOptions) > 1) { ?>
            <?php foreach (array_slice($variantOptions, 0, 4) as $variantProduct) { ?>
                <?php
                $optionLabel = (string)$variantProduct->getTitle();
                if ($optionMode === 'color' && trim((string)$variantProduct->getColor()) !== '') {
                    $optionLabel = trim((string)$variantProduct->getColor());
                } elseif ($optionMode === 'size' && trim((string)$variantProduct->getSize()) !== '') {
                    $optionLabel = trim((string)$variantProduct->getSize());
                }
                ?>
                <a
                    class="webshop-product-card2__option webshop-product-card2__option--<?=$optionMode?> <?=(int)$variantProduct->getId() === (int)$product->getId() ? 'is-selected' : ''?>"
                    href="<?=$variantProduct->getDetailUrl($pageId)?>"
                    <?php if ($optionMode === 'color') { ?>style="--option-color: <?=$getColorCssValue($optionLabel)?>"<?php } ?>
                    title="<?=htmlspecialchars((string)$variantProduct->getTitle(), ENT_QUOTES, 'UTF-8')?>"
                >
                    <?php if ($optionMode === 'color') { ?>
                        <span><?=htmlspecialchars($optionLabel, ENT_QUOTES, 'UTF-8')?></span>
                    <?php } else { ?>
                        <?=htmlspecialchars($optionLabel, ENT_QUOTES, 'UTF-8')?>
                    <?php } ?>
                </a>
            <?php } ?>
        <?php } else { ?>
            <span class="webshop-product-card2__stock <?=$isInStock ? 'is-available' : 'is-unavailable'?>">
                <?=$isInStock ? t('webshop_product_available_direct', 'Direct leverbaar') : t('webshop_product_unavailable', 'Niet op voorraad')?>
            </span>
        <?php } ?>
    </div>

    <a class="webshop-product-card2__action" href="<?=$detailUrl?>">
        <span><?=t('webshop_product_view_button', 'Bekijk product')?></span>
        <i class="fas fa-arrow-right" aria-hidden="true"></i>
    </a>
</article>
