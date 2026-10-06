<?php

$pageId = (int)($pageId ?? 0);
$items = is_array($entities ?? null) ? $entities : [];
$groupsByMainGroupId = is_array($productGroupsByMainGroupId ?? null) ? $productGroupsByMainGroupId : [];

if (!empty($items)) { ?>
    <div class="webshop-product-main-group-dropdown" role="group" aria-label="<?=htmlspecialchars(t('webshop_main_group_dropdown_label', 'Productcategorieën'), ENT_QUOTES, 'UTF-8')?>">
        <ul class="webshop-product-main-group-dropdown__main-groups">
            <?php foreach ($items as $mainGroup) {
                $mainGroupId = method_exists($mainGroup, 'getId') ? (int)$mainGroup->getId() : 0;
                $title = method_exists($mainGroup, 'getTitle') ? trim((string)$mainGroup->getTitle()) : '';
                $href = method_exists($mainGroup, 'getDetailUrl') ? (string)$mainGroup->getDetailUrl($pageId) : '#';
                $children = $groupsByMainGroupId[$mainGroupId] ?? [];
                ?>
                <li class="webshop-product-main-group-dropdown__item">
                    <a class="webshop-product-main-group-dropdown__main-link" href="<?=htmlspecialchars($href, ENT_QUOTES, 'UTF-8')?>">
                        <span><?=htmlspecialchars($title, ENT_QUOTES, 'UTF-8')?></span>
                        <i class="fas fa-chevron-right" aria-hidden="true"></i>
                    </a>
                    <button
                        type="button"
                        class="webshop-product-main-group-dropdown__open-groups"
                        data-open-product-groups
                        aria-label="<?=htmlspecialchars(sprintf(t('webshop_main_group_dropdown_open', 'Toon productgroepen van %s'), $title), ENT_QUOTES, 'UTF-8')?>"
                        aria-expanded="false"
                    >
                        <i class="fas fa-chevron-right" aria-hidden="true"></i>
                    </button>

                    <section class="webshop-product-main-group-dropdown__panel" aria-label="<?=htmlspecialchars(sprintf(t('webshop_main_group_dropdown_panel_label', 'Productgroepen binnen %s'), $title), ENT_QUOTES, 'UTF-8')?>">
                        <button type="button" class="webshop-product-main-group-dropdown__back" data-back-to-main-groups>
                            <i class="fas fa-arrow-left" aria-hidden="true"></i>
                            <span><?=htmlspecialchars(t('webshop_main_group_dropdown_back', 'Alle productcategorieën'), ENT_QUOTES, 'UTF-8')?></span>
                        </button>
                        <div class="webshop-product-main-group-dropdown__panel-header">
                            <span class="webshop-product-main-group-dropdown__eyebrow"><?=htmlspecialchars(t('webshop_main_group_dropdown_groups_label', 'Productgroepen'), ENT_QUOTES, 'UTF-8')?></span>
                            <h3><?=htmlspecialchars($title, ENT_QUOTES, 'UTF-8')?></h3>
                        </div>

                        <?php if (!empty($children)) { ?>
                            <div class="webshop-product-main-group-dropdown__groups" role="list">
                                <?php foreach ($children as $productGroup) {
                                    $childTitle = method_exists($productGroup, 'getTitle') ? trim((string)$productGroup->getTitle()) : '';
                                    $childHref = method_exists($productGroup, 'getDetailUrl') ? (string)$productGroup->getDetailUrl($pageId) : '#';
                                    ?>
                                    <div class="webshop-product-main-group-dropdown__group-item" role="listitem">
                                        <a href="<?=htmlspecialchars($childHref, ENT_QUOTES, 'UTF-8')?>">
                                            <span><?=htmlspecialchars($childTitle, ENT_QUOTES, 'UTF-8')?></span>
                                            <i class="fas fa-arrow-right" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } else { ?>
                            <p class="webshop-product-main-group-dropdown__empty">
                                <?=htmlspecialchars(t('webshop_main_group_dropdown_empty', 'Er zijn geen productgroepen beschikbaar.'), ENT_QUOTES, 'UTF-8')?>
                            </p>
                        <?php } ?>

                        <a class="webshop-product-main-group-dropdown__all-link" href="<?=htmlspecialchars($href, ENT_QUOTES, 'UTF-8')?>">
                            <span><?=htmlspecialchars(sprintf(t('webshop_main_group_dropdown_all', 'Bekijk alle %s'), $title), ENT_QUOTES, 'UTF-8')?></span>
                            <i class="fas fa-arrow-right" aria-hidden="true"></i>
                        </a>
                    </section>
                </li>
            <?php } ?>
        </ul>
    </div>
<?php }
