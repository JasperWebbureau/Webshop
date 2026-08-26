<?php
/**
 * @var $entity \Flexgrid\Modules\Webshop\Entity\WebshopProductGroup
 */

$productGroup = $entity;
$detailUrl = $productGroup->getDetailUrl($pageId);
$title = htmlspecialchars((string)$productGroup->getTitle(), ENT_QUOTES, 'UTF-8');
$description = trim(strip_tags((string)$productGroup->getDescription()));
?>
<article class="card webshop-product-group-card clickable" style="--cw:<?=$cardWidth?>;--cw-sm:6;--cw-xs:12">
    <a class="card__image webshop-product-group-card__image-link" href="<?=$detailUrl?>" aria-label="<?=$title?>">
        <img
            src="<?=$productGroup->getImage()->getResizeUrl(720, 460, 1)?>"
            alt="<?=$title?>"
            loading="lazy"
        >
    </a>
    <div class="card__content">
        <h3 class="card__title">
            <a href="<?=$detailUrl?>"><?=$title?></a>
        </h3>
        <?php if ($description !== '') { ?>
            <div class="card__description"><?=$description?></div>
        <?php } ?>
        <a class="button button-primary button-small" href="<?=$detailUrl?>">
            <?=t('webshop_product_group_view_button', 'Bekijk producten')?>
        </a>
    </div>
</article>
