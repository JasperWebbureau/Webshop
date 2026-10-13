<?php
/**
 * @var \Flexgrid\Modules\Webshop\Entity\WebshopProduct $entity
 * @var int $pageId
 */

use Flexgrid\Modules\Webshop\Service\PriceService;

$product = $entity;
$priceService = new PriceService();
$price = (float)$product->getPrice();
$activePrice = $priceService->getUnitPrice($product);
$hasSale = $activePrice < $price;
$url = (string)$product->getDetailUrl((int)$pageId);
$title = method_exists($product, 'getTitleShort') ? trim((string)$product->getTitleShort()) : '';
$title = $title !== '' ? $title : (string)$product->getTitle();
$manufacturer = trim((string)$product->getManufacturer());
$sku = trim((string)$product->getSku());
?>
<article class="card card-webshop-search-result clickable">

        <div class="card__media">
            <img
                src="<?=htmlspecialchars((string)$product->getImage()->getResizeUrl(240, 200, 0), ENT_QUOTES, 'UTF-8')?>"
                alt=""
                loading="lazy"
            >
        </div>
        <div class="card__content">
            <h3 class="card__title"><?=htmlspecialchars($title, ENT_QUOTES, 'UTF-8')?></h3>
            <?php if ($manufacturer !== '' || $sku !== '') { ?>
                <p class="card__description"><?=htmlspecialchars(implode(' · ', array_filter([$manufacturer, $sku], static function ($value) { return $value !== ''; })), ENT_QUOTES, 'UTF-8')?></p>
            <?php } ?>
            <div class="card__prices">
                <?php if ($hasSale) { ?><del class="card__price-old"><?=$priceService->format($price)?></del><?php } ?>
                <strong class="card__price"><?=$priceService->format($activePrice)?></strong>
            </div>
            <a class="card__link" href="<?=htmlspecialchars($url, ENT_QUOTES, 'UTF-8')?>">
                <i class="fas fa-arrow-right card__arrow" aria-hidden="true"></i>
            </a>
        </div>

</article>
