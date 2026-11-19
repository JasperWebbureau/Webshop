<?php
/** @var $entity \Flexgrid\Modules\Webshop\Entity\WebshopProductGroup */
$detailUrl = htmlspecialchars((string)$entity->getDetailUrl($pageId), ENT_QUOTES, 'UTF-8');
$title = htmlspecialchars((string)$entity->getTitle(), ENT_QUOTES, 'UTF-8');
$description = trim(strip_tags((string)$entity->getDescription()));
$image = $entity->getImage();
?>
<article class="card card-product-group webshop-product-group-card clickable" style="--cw:<?=max(1, min(12, (int)$cardWidth))?>;--cw-sm:6;--cw-xs:12">
    <div class="card__media card__image">
        <?php if ($image->exists()) { ?>
            <img src="<?=htmlspecialchars($image->getResizeUrl(720, 460, 0), ENT_QUOTES, 'UTF-8')?>" alt="<?=$title?>" loading="lazy">
        <?php } else { ?>
            <i class="fas fa-box-open" aria-hidden="true"></i>
        <?php } ?>
    </div>
    <div class="card__content">
        <h3 class="card__title"><?=$title?></h3>
        <?php if ($description !== '') { ?>
            <div class="card__description"><?=htmlspecialchars($description, ENT_QUOTES, 'UTF-8')?></div>
        <?php } ?>
        <div class="card__read-more"><a class="button button-primary" href="<?=$detailUrl?>"><?=t('webshop_product_group_view_button', 'Bekijk producten')?></a></div>
    </div>
</article>
