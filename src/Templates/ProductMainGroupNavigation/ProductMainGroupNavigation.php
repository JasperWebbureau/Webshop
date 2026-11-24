<?php

$navigationId = uniqid('webshop-category-navigation-');
?>
<nav class="webshop-category-navigation" data-webshop-category-navigation
     aria-label="<?=htmlspecialchars(t('webshop_category_navigation_label', 'Productcategorieën'), ENT_QUOTES, 'UTF-8')?>">
    <ul class="webshop-category-navigation__list">
        <?php foreach ($items as $index => $item) {
            $mainGroup = $item['entity'];
            $title = (string)$mainGroup->getTitle();
            $panelId = $navigationId . '-' . $index;
            ?>
            <li class="webshop-category-navigation__item">
                <a class="webshop-category-navigation__link" href="<?=htmlspecialchars($mainGroup->getDetailUrl($pageId), ENT_QUOTES, 'UTF-8')?>">
                    <?=htmlspecialchars($title, ENT_QUOTES, 'UTF-8')?>
                </a>
                <button type="button" class="webshop-category-navigation__toggle"
                        aria-label="<?=htmlspecialchars(sprintf(t('webshop_category_navigation_toggle', 'Toon productgroepen van %s'), $title), ENT_QUOTES, 'UTF-8')?>"
                        aria-expanded="false" aria-controls="<?=$panelId?>">
                    <i class="fas fa-chevron-down" aria-hidden="true"></i>
                </button>
                <ul class="webshop-category-navigation__dropdown" id="<?=$panelId?>" hidden>
                    <?php foreach ($item['groups'] as $group) { ?>
                        <li>
                            <a href="<?=htmlspecialchars($group->getDetailUrl($pageId), ENT_QUOTES, 'UTF-8')?>">
                                <?=htmlspecialchars((string)$group->getTitle(), ENT_QUOTES, 'UTF-8')?>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>
    </ul>
</nav>
