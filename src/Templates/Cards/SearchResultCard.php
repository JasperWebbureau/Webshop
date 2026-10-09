<?php
/**
 * @var \Flexgrid\Modules\Webshop\Entity\WebshopProduct $entity
 * @var int $pageId
 */

$product = $entity;
$price = (float)$product->getPrice();
$salePrice = (float)$product->getSalePrice();
$activePrice = $salePrice > 0 && $salePrice < $price ? $salePrice : $price;
$url = (string)$product->getDetailUrl((int)$pageId);
?>
<article class="card card-webshop-search-result">
    <a class="card__media" href="<?=htmlspecialchars($url, ENT_QUOTES, 'UTF-8')?>">
        <img
            src="<?=htmlspecialchars((string)$product->getImage()->getResizeUrl(160, 120, 1), ENT_QUOTES, 'UTF-8')?>"
            alt=""
            loading="lazy"
        >
    </a>
    <div class="card__content">
        <a class="card__title" href="<?=htmlspecialchars($url, ENT_QUOTES, 'UTF-8')?>">
            <?=htmlspecialchars((string)$product->getTitle(), ENT_QUOTES, 'UTF-8')?>
        </a>
        <?php if (trim((string)$product->getSku()) !== '') { ?>
            <small class="card__description"><?=htmlspecialchars((string)$product->getSku(), ENT_QUOTES, 'UTF-8')?></small>
        <?php } ?>
        <strong class="card__price">&euro; <?=number_format($activePrice, 2, ',', '.')?></strong>
    </div>
</article>
