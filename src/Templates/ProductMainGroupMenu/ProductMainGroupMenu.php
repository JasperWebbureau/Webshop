<?php

$limit = (int)($limit ?? 99);
$groupLimit = (int)($groupLimit ?? 99);
$pageId = (int)($pageId ?? 0);
$items = array_slice($entities ?? [], 0, $limit > 0 ? $limit : 99);
$repository = $productGroupRepository ?? null;

foreach ($items as $mainGroup) {
    $title = method_exists($mainGroup, 'getTitle') ? (string)$mainGroup->getTitle() : '';
    $href = method_exists($mainGroup, 'getDetailUrl') ? (string)$mainGroup->getDetailUrl($pageId) : '#';
    $intro = method_exists($mainGroup, 'getIntroSentence') ? trim((string)$mainGroup->getIntroSentence()) : '';
    $image = method_exists($mainGroup, 'getImage') ? $mainGroup->getImage() : null;
    $imageUrl = $image && method_exists($image, 'getResizeUrl') ? (string)$image->getResizeUrl(760, 560, 1) : '';
    $children = [];

    if ($repository && method_exists($repository, 'getByMainGroupId') && method_exists($mainGroup, 'getId')) {
        $children = $repository->getByMainGroupId((int)$mainGroup->getId(), $groupLimit > 0 ? $groupLimit : 99);
    }
    ?>
    <li class="webshop-product-main-group-menu__item <?=!empty($children) ? 'has-dropdown' : ''?>">
        <a class="webshop-product-main-group-menu__link" href="<?=htmlspecialchars($href, ENT_QUOTES, 'UTF-8')?>" aria-label="Product hoofdgroep: <?=htmlspecialchars($title, ENT_QUOTES, 'UTF-8')?>">
            <span><?=htmlspecialchars($title, ENT_QUOTES, 'UTF-8')?></span>
            <?php if (!empty($children)) { ?>
                <i class="fas fa-chevron-down" aria-hidden="true"></i>
            <?php } ?>
        </a>

        <?php if (!empty($children)) { ?>
            <div class="webshop-product-main-group-menu__dropdown">
                <div class="webshop-product-main-group-menu__panel">
                    <div class="webshop-product-main-group-menu__content">
                        <p class="webshop-product-main-group-menu__eyebrow"><?=htmlspecialchars($title, ENT_QUOTES, 'UTF-8')?></p>
                        <strong class="webshop-product-main-group-menu__title">
                            <?=htmlspecialchars($intro !== '' ? $intro : $title, ENT_QUOTES, 'UTF-8')?>
                        </strong>

                        <ul class="webshop-product-main-group-menu__children">
                            <?php foreach ($children as $productGroup) {
                                $childTitle = method_exists($productGroup, 'getTitle') ? (string)$productGroup->getTitle() : '';
                                $childHref = method_exists($productGroup, 'getDetailUrl') ? (string)$productGroup->getDetailUrl($pageId) : '#';
                                ?>
                                <li>
                                    <a href="<?=htmlspecialchars($childHref, ENT_QUOTES, 'UTF-8')?>" aria-label="Productgroep: <?=htmlspecialchars($childTitle, ENT_QUOTES, 'UTF-8')?>">
                                        <span><?=htmlspecialchars($childTitle, ENT_QUOTES, 'UTF-8')?></span>
                                        <i class="fas fa-chevron-right" aria-hidden="true"></i>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>

                        <a class="webshop-product-main-group-menu__all-link" href="<?=htmlspecialchars($href, ENT_QUOTES, 'UTF-8')?>">
                            <span><?=htmlspecialchars(sprintf(t('webshop_main_group_menu_all', 'Bekijk alle %s'), $title), ENT_QUOTES, 'UTF-8')?></span>
                            <i class="fas fa-arrow-right" aria-hidden="true"></i>
                        </a>
                    </div>

                    <?php if ($imageUrl !== '') { ?>
                        <a class="webshop-product-main-group-menu__media" href="<?=htmlspecialchars($href, ENT_QUOTES, 'UTF-8')?>" aria-label="<?=htmlspecialchars(sprintf(t('webshop_main_group_menu_image_label', 'Bekijk %s'), $title), ENT_QUOTES, 'UTF-8')?>">
                            <img src="<?=htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8')?>" alt="<?=htmlspecialchars($title, ENT_QUOTES, 'UTF-8')?>" loading="lazy">
                            <span><?=htmlspecialchars($intro !== '' ? $intro : $title, ENT_QUOTES, 'UTF-8')?></span>
                        </a>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    </li>
<?php }
