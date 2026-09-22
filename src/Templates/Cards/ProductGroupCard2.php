<?php
/**
 * @var $entity \Flexgrid\Modules\Webshop\Entity\WebshopProductGroup
 */

$productGroup = $entity;
$detailUrl = $productGroup->getDetailUrl($pageId);
$title = htmlspecialchars((string)$productGroup->getTitle(), ENT_QUOTES, 'UTF-8');
$cardIndex = (int)($cardIndex ?? 0);

?>
<article class="webshop-product-group-card2 clickable" style="--cw:<?=$cardWidth?>;--cw-sm:6;--cw-xs:12;--webshop-card-accent:<?=$accentColor?>">
    <a class="webshop-product-group-card2__link" href="<?=$detailUrl?>" aria-label="<?=$title?>">
        <span class="webshop-product-group-card2__image">
            <img
                src="<?=$productGroup->getImage()->getResizeUrl(720, 520, 1)?>"
                alt="<?=$title?>"
                loading="lazy"
            >
        </span>
        <span class="webshop-product-group-card2__content">
            <h3 class="webshop-product-group-card2__title"><?=$title?></h3>
            <span class="webshop-product-group-card2__arrow" aria-hidden="true">
                <i class="fas fa-arrow-right"></i>
            </span>
        </span>
    </a>
</article>
