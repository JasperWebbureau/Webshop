<?php
/**
 * @var $entity \Flexgrid\Modules\Webshop\Entity\WebshopProductMainGroup
 */

$mainGroup = $entity;
$detailUrl = $mainGroup->getDetailUrl($pageId);
$title = htmlspecialchars((string)$mainGroup->getTitle(), ENT_QUOTES, 'UTF-8');
$description = trim(strip_tags((string)$mainGroup->getDescription()));
$intro = trim((string)$mainGroup->getIntroSentence());
$cardIndex = (int)($cardIndex ?? 0);
require_once path('Flexgrid/Modules/Webshop/src/Templates/Cards/Snippets/CardColors.php');
$accentColor = webshopCardAccentColor($cardIndex);
?>
<article class="card webshop-product-main-group-card clickable" style="--cw:<?=$cardWidth?>;--cw-sm:6;--cw-xs:12;--webshop-card-accent:<?=$accentColor?>">
    <a class="card__image webshop-product-main-group-card__image-link" href="<?=$detailUrl?>" aria-label="<?=$title?>">
        <img
            src="<?=$mainGroup->getImage()->getResizeUrl(720, 460, 1)?>"
            alt="<?=$title?>"
            loading="lazy"
        >
    </a>
    <div class="card__content">
        <h3 class="card__title">
            <a href="<?=$detailUrl?>"><?=$title?></a>
        </h3>
        <?php if ($intro !== '') { ?>
            <strong class="webshop-product-main-group-card__intro"><?=htmlspecialchars($intro, ENT_QUOTES, 'UTF-8')?></strong>
        <?php } ?>
        <?php if ($description !== '') { ?>
            <div class="card__description"><?=$description?></div>
        <?php } ?>
        <a class="button button-primary button-small" href="<?=$detailUrl?>">
            <?=t('webshop_product_main_group_view_button', 'Bekijk hoofdgroep')?>
        </a>
    </div>
</article>
