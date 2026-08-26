<?php
use Flexgrid\Modules\Webshop\Controller\WebshopController;

$items = $items ?? [];
$summary = $summary ?? ['quantity' => count($items)];
$previewItems = array_slice($items, 0, 3);
$favoritePage = '';
$productGridPageId = 0;
$configurationErrors = [];
try {
    $favoritePage = WebshopController::getFavoriteUrl();
} catch (\Throwable $exception) {
    $configurationErrors[] = $exception->getMessage();
}
try {
    $productGridPageId = WebshopController::getProductGridPageId();
} catch (\Throwable $exception) {
    $configurationErrors[] = $exception->getMessage();
}

?>

<div class="webshop-header-summary webshop-header-summary--favorite webshop-favorite-summary" tabindex="0">
    <a class="webshop-header-summary__trigger" href="<?=$favoritePage !== '' ? $favoritePage : '#'?>" aria-label="<?=t('webshop_favorite_summary_open', 'Open favorieten')?>">
        <span class="webshop-header-summary__icon">
            <i class="fas fa-heart" aria-hidden="true"></i>
        </span>
        <span class="webshop-header-summary__count js-webshop-favorite-count"><?=(int)($summary['quantity'] ?? 0)?></span>
    </a>

    <div class="webshop-header-summary__popover">
        <div class="webshop-header-summary__popover-header">
            <strong><?=t('webshop_favorite_summary_title', 'Favorieten')?></strong>
            <span><?=sprintf(t('webshop_favorite_summary_count', '%d items'), (int)($summary['quantity'] ?? 0))?></span>
        </div>

        <?php if (empty($previewItems)) { ?>
            <p class="webshop-header-summary__empty"><?=t('webshop_favorite_summary_empty', 'Je hebt nog geen favorieten.')?></p>
        <?php } else { ?>
            <div class="webshop-header-summary__items">
                <?php foreach ($previewItems as $product) {
                    $productDetailUrl = $productGridPageId > 0 ? $product->getDetailUrl($productGridPageId) : '#';
                    ?>
                    <a class="webshop-header-summary__item" href="<?=$productDetailUrl?>">
                        <span class="webshop-header-summary__item-image">
                            <img src="<?=$product->getImage()->getResizeUrl(140, 140, 1)?>" alt="<?=htmlspecialchars((string)$product->getTitle(), ENT_QUOTES, 'UTF-8')?>" loading="lazy">
                        </span>
                        <span class="webshop-header-summary__item-content">
                            <strong><?=htmlspecialchars((string)$product->getTitle(), ENT_QUOTES, 'UTF-8')?></strong>
                            <?php if (trim((string)$product->getWebshopProductGroupValue()) !== '') { ?>
                                <small><?=htmlspecialchars((string)$product->getWebshopProductGroupValue(), ENT_QUOTES, 'UTF-8')?></small>
                            <?php } ?>
                        </span>
                    </a>
                <?php } ?>
            </div>
            <?php if (count($items) > count($previewItems)) { ?>
                <div class="webshop-header-summary__more">
                    <?=t('webshop_favorite_summary_more', 'Meer producten in je favorieten')?>
                </div>
            <?php } ?>
        <?php } ?>

        <?php if (!empty($configurationErrors)) { ?>
            <div class="webshop-header-summary__configuration-error">
                <?=htmlspecialchars(implode(' ', $configurationErrors), ENT_QUOTES, 'UTF-8')?>
            </div>
        <?php } ?>

        <div class="webshop-header-summary__actions">
            <?php if ($favoritePage !== '') { ?>
                <a class="button button-outline button-small" href="<?=$favoritePage?>">
                    <?=t('webshop_favorite_summary_button', 'Alle favorieten')?>
                </a>
            <?php } ?>
        </div>
    </div>
</div>
