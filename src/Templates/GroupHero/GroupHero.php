<?php

$currentGroup = $currentGroup ?? null;
$productCount = (int)($productCount ?? count($entities ?? []));


if ($currentGroup && method_exists($currentGroup, 'getTitle')) {
    $breadcrumbController = new \App\Block\Controller\NavController();
    $breadcrumbs = $breadcrumbController->BreadCrumbs();
    $groupTitle = (string)$currentGroup->getTitle();
    $groupDescription = trim((string)$currentGroup->getDescription());
    $groupImage = method_exists($currentGroup, 'getImage') ? $currentGroup->getImage() : null;
    $hasGroupImage = $groupImage && ((int)$groupImage->getMediaId() > 0 || trim((string)$groupImage) !== '');
    ?>
    <section class="webshop-product-grid-intro">
        <div class="webshop-product-grid-intro__content">
            <?=$breadcrumbs?>
            <h1><?=htmlspecialchars($groupTitle, ENT_QUOTES, 'UTF-8')?></h1>
            <?php if ($groupDescription !== '') { ?>
                <div class="webshop-product-grid-intro__description"><?=$groupDescription?></div>
            <?php } ?>
            <p class="webshop-product-grid-intro__count">
                <?=sprintf(t('webshop_product_grid_group_count', '%d producten'), $productCount)?>
            </p>
        </div>
        <?php if ($hasGroupImage) { ?>
            <div class="webshop-product-grid-intro__image">
                <img src="<?=$groupImage->getResizeUrl(1100, 520, 1)?>" alt="<?=htmlspecialchars($groupTitle, ENT_QUOTES, 'UTF-8')?>" loading="eager">
            </div>
        <?php } ?>
    </section>
<?php } ?>