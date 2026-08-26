<?php
/**
 * @var $entity \Flexgrid\Modules\Webshop\Entity\WebshopProduct
 */

use Flexgrid\Response\TemplateResponse;

$product = $entity;
$price = (float)$product->getPrice();
$salePrice = (float)$product->getSalePrice();
$activePrice = $salePrice > 0 && $salePrice < $price ? $salePrice : $price;
$variantProducts = method_exists($product, 'getVariantClusterProducts') ? $product->getVariantClusterProducts(true) : [$product];
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
$isInStock = (int)$product->getTrackStock() !== 1 || (int)$product->getStock() > 0;
$description = trim(strip_tags((string)$product->getShortDescription()));
if ($description === '') {
    $description = trim(strip_tags((string)$product->getDescription()));
}
if (strlen($description) > 140) {
    $description = substr($description, 0, 137) . '...';
}
$detailUrl = $product->getDetailUrl($pageId);
?>
<article class="card webshop-product-card clickable" style="--cw:<?=$cardWidth?>;--cw-sm:6;--cw-xs:12">
    <a class="card__image webshop-product-card__image-link" href="<?=$detailUrl?>" aria-label="<?=htmlspecialchars((string)$product->getTitle(), ENT_QUOTES, 'UTF-8')?>">
        <img
            src="<?=$product->getImage()->getResizeUrl(720, 540, 1)?>"
            alt="<?=htmlspecialchars((string)$product->getTitle(), ENT_QUOTES, 'UTF-8')?>"
            loading="lazy"
        >
    </a>

    <div class="card__content">
        <div class="card__meta">
            <?php if ((string)$product->getSku() !== '') { ?>
                <span><?=htmlspecialchars((string)$product->getSku(), ENT_QUOTES, 'UTF-8')?></span>
            <?php } ?>
            <span class="webshop-product-card__availability <?=$isInStock ? 'is-available' : 'is-unavailable'?>">
                <?=$isInStock ? t('webshop_product_available', 'Op voorraad') : t('webshop_product_unavailable', 'Niet op voorraad')?>
            </span>
        </div>

        <h3 class="card__title">
            <a href="<?=$detailUrl?>"><?=htmlspecialchars((string)$product->getTitle(), ENT_QUOTES, 'UTF-8')?></a>
        </h3>

        <?php if ($description !== '') { ?>
            <div class="card__description"><?=$description?></div>
        <?php } ?>

        <div class="webshop-product-card__buy">
            <div class="webshop-product-card__price">
                <?php if ($salePrice > 0 && $salePrice < $price) { ?>
                    <span class="webshop-product-card__price-old">&euro; <?=number_format($price, 2, ',', '.')?></span>
                <?php } ?>
                <span class="webshop-product-card__price-current"><?=count($variantProducts) > 1 ? t('webshop_product_card_from', 'Vanaf') . ' ' : ''?>&euro; <?=number_format($activePrice, 2, ',', '.')?></span>
            </div>
            <?php if(false){ ?>
            <?=new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/AddToCart/AddToCart.php', [
                'product' => $product,
                'showQuantity' => false,
            ])?>
            <?php } ?>
        </div>

        <div class="card__actions">
            <a class="button button-outline button-small" href="<?=$detailUrl?>">
                <?=t('webshop_product_view_button', 'Bekijk product')?>
            </a>
        </div>
    </div>
</article>
