<?php
/**
 * @var $entity \Flexgrid\Modules\Webshop\Entity\WebshopProductMainGroup|null
 */
if (!$entity) {
    return;
}
$breadcrumbController = new \App\Block\Controller\NavController();
$breadcrumbs = $breadcrumbController->BreadCrumbs();

$title = htmlspecialchars((string)$entity->getTitle(), ENT_QUOTES, 'UTF-8');
$intro = trim((string)$entity->getIntroSentence());
$description = trim((string)$entity->getDescription());
$descriptionText = trim(strip_tags(html_entity_decode($description, ENT_QUOTES, 'UTF-8')));
$descriptionLength = function_exists('mb_strlen') ? mb_strlen($descriptionText) : strlen($descriptionText);
$hasCollapsibleDescription = $descriptionLength >= 200;
$image = $entity->getImage();
$buttonUrl = $entity->getDetailUrl($productGroupPageId);
?>
<section class="webshop-main-group-banner">
    <div class="webshop-main-group-banner__content">
        <?=$breadcrumbs?>
        <?php if ($intro !== '') { ?>
            <p class="webshop-main-group-banner__intro"><?=htmlspecialchars($intro, ENT_QUOTES, 'UTF-8')?></p>
        <?php } ?>
        <h1><?=$title?></h1>
        <?php if ($description !== '') { ?>
            <div
                class="webshop-main-group-banner__description<?=$hasCollapsibleDescription ? ' webshop-main-group-banner__description--collapsible' : ''?>"
                <?=$hasCollapsibleDescription ? 'data-webshop-main-group-description' : ''?>
            >
                <div class="webshop-main-group-banner__description-content">
                    <?=$description?>
                </div>
            </div>
            <?php if ($hasCollapsibleDescription) { ?>
                <button
                    class="webshop-main-group-banner__description-toggle"
                    type="button"
                    data-webshop-main-group-description-toggle
                    data-read-less-label="<?=htmlspecialchars(t('webshop_main_group_read_less', 'Lees minder'), ENT_QUOTES, 'UTF-8')?>"
                    aria-expanded="false"
                >
                    <?=t('webshop_main_group_read_more', 'Lees meer')?>
                </button>
            <?php } ?>
        <?php } ?>
        <?php if (trim((string)$buttonText) !== '') { ?>
            <a class="button button-primary" href="<?=$buttonUrl?>"><?=htmlspecialchars((string)$buttonText, ENT_QUOTES, 'UTF-8')?></a>
        <?php } ?>
    </div>
    <div class="webshop-main-group-banner__image">
        <img src="<?=$image->getResizeUrl(1280, 720, 1)?>" alt="<?=$title?>" loading="eager">
    </div>
</section>
