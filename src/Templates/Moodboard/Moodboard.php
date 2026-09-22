<?php
/**
 * @var $moodboard \Flexgrid\Modules\Webshop\Entity\WebshopMoodboard
 * @var $items \Flexgrid\Modules\Webshop\Entity\WebshopMoodboardItem[]
 * @var $productGridPageId int
 */

$title = htmlspecialchars((string)$moodboard->getTitle(), ENT_QUOTES, 'UTF-8');
$eyebrow = htmlspecialchars((string)$moodboard->getEyebrow(), ENT_QUOTES, 'UTF-8');
$intro = htmlspecialchars((string)$moodboard->getIntro(), ENT_QUOTES, 'UTF-8');
$buttonText = trim((string)$moodboard->getButtonText()) !== ''
    ? htmlspecialchars((string)$moodboard->getButtonText(), ENT_QUOTES, 'UTF-8')
    : htmlspecialchars(t('webshop_moodboard_all_products', 'Bekijk alle producten'), ENT_QUOTES, 'UTF-8');
$allProductsUrl = '';
if ((int)$productGridPageId > 0) {
    try {
        $allProductsUrl = \Flexgrid\Modules\Webshop\Controller\WebshopController::getProductGridUrl();
    } catch (\Throwable $exception) {
        $allProductsUrl = '';
    }
}
$visibleItems = [];

foreach ($items as $item) {
    $product = $item->getWebshopProductParent();
    if (!$product || (int)$product->getId() <= 0) {
        continue;
    }

    if (method_exists($product, 'getIsActive') && (int)$product->getIsActive() !== 1) {
        continue;
    }

    if (method_exists($product, 'getStatus') && (string)$product->getStatus() !== 'published') {
        continue;
    }

    $visibleItems[] = [
        'item' => $item,
        'product' => $product,
    ];
}
?>
<section class="webshop-moodboard" data-webshop-moodboard>
    <div class="webshop-moodboard__header">
        <?php if ($eyebrow !== '') { ?>
            <div class="webshop-moodboard__eyebrow"><?=$eyebrow?></div>
        <?php } ?>
        <h2><?=$title?></h2>
        <?php if ($intro !== '') { ?>
            <p><?=$intro?></p>
        <?php } ?>
    </div>

    <div class="webshop-moodboard__layout">
        <div class="webshop-moodboard__scene">
            <img
                src="<?=$moodboard->getImage()->getResizeUrl(1500, 960, 1)?>"
                alt="<?=$title?>"
                loading="lazy"
            >

            <?php foreach ($visibleItems as $index => $entry) {
                $item = $entry['item'];
                $product = $entry['product'];
                $number = str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT);
                ?>
                <button
                    class="webshop-moodboard__hotspot <?=$index === 0 ? 'is-active' : ''?>"
                    type="button"
                    style="--hotspot-x: <?=number_format((float)$item->getPositionX(), 2, '.', '')?>%; --hotspot-y: <?=number_format((float)$item->getPositionY(), 2, '.', '')?>%;"
                    data-webshop-moodboard-hotspot="<?=(int)$product->getId()?>"
                    aria-label="<?=htmlspecialchars((string)$product->getTitle(), ENT_QUOTES, 'UTF-8')?>"
                >
                    <span class="webshop-moodboard__hotspot-number"><?=$number?></span>
                    <span class="webshop-moodboard__hotspot-plus">+</span>
                </button>
            <?php } ?>
        </div>

        <aside class="webshop-moodboard__products">
            <div class="webshop-moodboard__products-header">
                <h3><?=t('webshop_moodboard_products_title', 'Producten in deze look')?></h3>
                <span><?=count($visibleItems)?> <?=t('webshop_moodboard_items', 'items')?></span>
            </div>

            <div class="webshop-moodboard__product-list">
                <?php foreach ($visibleItems as $index => $entry) {
                    $product = $entry['product'];
                    $price = (float)$product->getPrice();
                    $salePrice = (float)$product->getSalePrice();
                    $activePrice = $salePrice > 0 && $salePrice < $price ? $salePrice : $price;
                    $detailUrl = $productGridPageId > 0 ? $product->getDetailUrl($productGridPageId) : '#';
                    $number = str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT);
                    $meta = trim((string)$product->getColor());
                    if (trim((string)$product->getSize()) !== '') {
                        $meta .= ($meta !== '' ? ' - ' : '') . trim((string)$product->getSize());
                    }
                    ?>
                    <a
                        class="webshop-moodboard__product <?=$index === 0 ? 'is-active' : ''?>"
                        href="<?=$detailUrl?>"
                        data-webshop-moodboard-product="<?=(int)$product->getId()?>"
                    >
                        <span class="webshop-moodboard__product-image">
                            <img
                                src="<?=$product->getImage()->getResizeUrl(280, 220, 1)?>"
                                alt="<?=htmlspecialchars((string)$product->getTitle(), ENT_QUOTES, 'UTF-8')?>"
                                loading="lazy"
                            >
                        </span>
                        <span class="webshop-moodboard__product-content">
                            <span class="webshop-moodboard__product-number"><?=$number?></span>
                            <strong><?=htmlspecialchars((string)$product->getTitle(), ENT_QUOTES, 'UTF-8')?></strong>
                            <?php if ($meta !== '') { ?>
                                <small><?=htmlspecialchars($meta, ENT_QUOTES, 'UTF-8')?></small>
                            <?php } ?>
                            <span class="webshop-moodboard__product-price">&euro; <?=number_format($activePrice, 2, ',', '.')?></span>
                        </span>
                        <i class="fas fa-arrow-right" aria-hidden="true"></i>
                    </a>
                <?php } ?>
            </div>

            <?php if ($allProductsUrl !== '') { ?>
                <a class="webshop-moodboard__all-link" href="<?=htmlspecialchars($allProductsUrl, ENT_QUOTES, 'UTF-8')?>">
                    <span><?=$buttonText?></span>
                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                </a>
            <?php } ?>
        </aside>
    </div>
</section>
