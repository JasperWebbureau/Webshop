<?php

/**
 * @var \App\Entity\Product $product;
 *
 */

$width = $width ?: 4;

$link = $product->getDetailUrl(46);
$children = $product->getProductChildren();
if(!empty($children)){
    $link = $children[0]->getDetailUrl(46);
}
?>
<div class="card card--product clickable" style="--cw:<?=$width?>; --cw-xs:6">
    <div class="card__image">
        <img src="<?=$product->getImage()->getResizeUrl(300, 300, 1);?>">
    </div>
    <div class="card__content">
        <div class="card__title">
            <h3><?=$product->getTitle()?></h3>

        </div>
        <div class="card__price">
            <?=$product->getPriceHtml()?>
        </div>

        <div class="card__read-more">
            <a href="<?=$link?>" class=""><i class="fas fa-chevron-right"></i></a>
        </div>
    </div>

</div>
