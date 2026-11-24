<?php
use Flexgrid\Modules\Webshop\Service\PriceService;
use Flexgrid\Response\TemplateResponse;
use Flexgrid\Utils\_String;

$product = $entity;
$pricing = (new PriceService())->getCardPricing($product);
$title = htmlspecialchars((string)$product->getTitle(), ENT_QUOTES, 'UTF-8');
$detailUrl = htmlspecialchars((string)$product->getDetailUrl($pageId ?? 0), ENT_QUOTES, 'UTF-8');
$description = trim(strip_tags((string)($product->getShortDescription() ?: $product->getDescription())));
$description = (new _String($description))->shorten(140, true, false);
$isInStock = (int)$product->getTrackStock() !== 1 || (int)$product->getStock() > 0;
$image = $product->getImage();
?>
<article class="card card-product card-product--<?=$cardModifier?> clickable" style="--cw:<?=max(1, min(12, (int)($cardWidth ?? 4)))?>;--cw-sm:6;--cw-xs:12">
    <div class="card__media">
        <?php if ($image->exists()) { ?>
            <img src="<?=htmlspecialchars($image->getResizeUrl(720, 540, 0), ENT_QUOTES, 'UTF-8')?>" alt="<?=$title?>" loading="lazy">
        <?php } else { ?>
            <i class="fas fa-box-open" aria-hidden="true"></i>
        <?php } ?>
        <?php if ($pricing['sale']) { ?><span class="card__tag"><?=t('webshop_product_card_sale', 'Aanbieding')?></span><?php } ?>
        <?=new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/Cards/Snippets/ProductCard2Favorite.php', ['product' => $product])?>
    </div>
    <div class="card__content">
        <h3 class="card__title"><?=$title?></h3>
        <?php if ($showDescription && $description !== '') { ?><div class="card__description"><?=htmlspecialchars($description, ENT_QUOTES, 'UTF-8')?></div><?php } ?>
        <p class="card__availability <?=$isInStock ? 'card__availability--available' : ''?>"><?=$isInStock ? t('webshop_product_available', 'Op voorraad') : t('webshop_product_unavailable', 'Niet op voorraad')?></p>
        <div class="card__price">
            <?php if (!$isInStock && !$pricing['hasPrice']) { ?>
                <span><?=t('webshop_product_price_pending', 'Prijs volgt')?></span>
            <?php } else { ?>
                <?php if ($pricing['sale']) { ?><del>&euro; <?=number_format($pricing['original'], 2, ',', '.')?></del><?php } ?>
                <span><?=$pricing['from'] ? t('webshop_product_card_from', 'Vanaf') . ' ' : ''?>&euro; <?=number_format($pricing['price'], 2, ',', '.')?></span>
            <?php } ?>
        </div>
        <div class="card__read-more"><a class="button button-primary" href="<?=$detailUrl?>"><?=t('webshop_product_view_button', 'Bekijk product')?></a></div>
    </div>
</article>
