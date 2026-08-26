<?php

$currentGroup = $currentGroup ?? null;

if ($currentGroup && method_exists($currentGroup, 'getTitle')) {
    $breadcrumbController = new \App\Block\Controller\NavController();
    $breadcrumbs = $breadcrumbController->BreadCrumbs();
    $groupTitle = (string)$currentGroup->getTitle();
    $groupDescription = trim((string)$currentGroup->getDescription());
    $groupDescription = str_replace(['<p>', '</p>'], ['', PHP_EOL], $groupDescription);
    $groupDescriptionText = trim(strip_tags(html_entity_decode($groupDescription, ENT_QUOTES, 'UTF-8')));
    $groupDescriptionSentences = [];

    if ($groupDescriptionText !== '') {
        preg_match_all('/[^.!?]+[.!?]+(?:\s+|$)|[^.!?]+$/u', $groupDescriptionText, $matches);
        $groupDescriptionSentences = array_values(array_filter(array_map('trim', $matches[0] ?? [])));
    }

    $groupDescriptionExcerpt = trim(implode(' ', array_slice($groupDescriptionSentences, 0, 2)));
    if ($groupDescriptionExcerpt === '') {
        $groupDescriptionExcerpt = $groupDescriptionText;
    }

    $hasCollapsibleDescription = count($groupDescriptionSentences) > 2;
    $groupImage = method_exists($currentGroup, 'getImage') ? $currentGroup->getImage() : null;
    $hasGroupImage = $groupImage && ((int)$groupImage->getMediaId() > 0 || trim((string)$groupImage) !== '');
    ?>
    <section class="webshop-product-grid-intro">
        <div class="webshop-product-grid-intro__content">
            <?=$breadcrumbs?>
            <h1><?=htmlspecialchars($groupTitle, ENT_QUOTES, 'UTF-8')?></h1>
            <?php if ($groupDescription !== '') { ?>
                <div
                    class="webshop-product-grid-intro__description<?=$hasCollapsibleDescription ? ' webshop-product-grid-intro__description--collapsible' : ''?>"
                    <?=$hasCollapsibleDescription ? 'data-webshop-group-hero-description' : ''?>
                >
                    <?php if ($hasCollapsibleDescription) { ?>
                        <div class="webshop-product-grid-intro__description-excerpt" data-webshop-group-hero-description-excerpt>
                            <p><?=htmlspecialchars($groupDescriptionExcerpt, ENT_QUOTES, 'UTF-8')?></p>
                        </div>
                        <div class="webshop-product-grid-intro__description-full" data-webshop-group-hero-description-full hidden>
                            <?=$groupDescription?>
                        </div>
                    <?php } else { ?>
                        <?=$groupDescription?>
                    <?php } ?>
                </div>
                <?php if ($hasCollapsibleDescription) { ?>
                    <button
                        class="webshop-product-grid-intro__description-toggle"
                        type="button"
                        data-webshop-group-hero-description-toggle
                        data-read-less-label="<?=htmlspecialchars(t('webshop_group_hero_read_less', 'Lees minder'), ENT_QUOTES, 'UTF-8')?>"
                        aria-expanded="false"
                    >
                        <?=t('webshop_group_hero_read_more', 'Lees meer')?>
                    </button>
                <?php } ?>
            <?php } ?>
            <p class="webshop-product-grid-intro__count">
                -product-count-
                <?=t('webshop_product_grid_group_count', 'producten')?>
            </p>
        </div>
        <?php if ($hasGroupImage) { ?>
            <div class="webshop-product-grid-intro__image">
                <img src="<?=$groupImage->getResizeUrl(1100, 420, 1)?>" alt="<?=htmlspecialchars($groupTitle, ENT_QUOTES, 'UTF-8')?>" loading="eager">
            </div>
        <?php } ?>
    </section>
<?php } ?>
