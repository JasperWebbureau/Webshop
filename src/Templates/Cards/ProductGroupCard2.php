<?php
/** @var $entity \Flexgrid\Modules\Webshop\Entity\WebshopProductGroup */
$detailUrl = htmlspecialchars((string)$entity->getDetailUrl($pageId), ENT_QUOTES, 'UTF-8');
$title = htmlspecialchars((string)$entity->getTitle(), ENT_QUOTES, 'UTF-8');
$image = $entity->getImage();
?>
<article class="card card-product-group card-product-group--visual webshop-product-group-card2 clickable" style="--cw:<?=max(1, min(12, (int)$cardWidth))?>;--cw-sm:6;--cw-xs:12">
    <div class="card__media webshop-product-group-card2__image">
        <?php if ($image->exists()) { ?>
            <img src="<?=htmlspecialchars($image->getResizeUrl(720, 460, 0), ENT_QUOTES, 'UTF-8')?>" alt="<?=$title?>" loading="lazy">
        <?php } else { ?>
            <i class="fas fa-box-open" aria-hidden="true"></i>
        <?php } ?>
    </div>
    <div class="card__content webshop-product-group-card2__content">
        <h3 class="card__title webshop-product-group-card2__title"><?=$title?></h3>
        <a class="webshop-product-group-card2__arrow" href="<?=$detailUrl?>" aria-label="<?=$title?>"><i class="fas fa-arrow-right" aria-hidden="true"></i></a>
    </div>
</article>
