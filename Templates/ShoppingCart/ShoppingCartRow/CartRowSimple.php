<?php
/**
* @var \App\Entity\Product $product
*/
$amount = $list['amount'];
?>
<div class="shopping-cart-row row">
    <div class="shopping-cart-row__image ">
        <a href="<?=$product->getDetailUrl(46) ?>">
        <figure>
            <img src="<?=$product->getImage()->getResizeUrl(50,50)?>">
        </figure>
        </a>
    </div>
    <div class="shopping-cart-row__content">
        <div class="shopping-cart-row__title">
            <a href="<?=$product->getDetailUrl(46) ?>" >
                <?=$product->getTitle()?>
            </a>
        </div>
        <div class="row">
            <div class="shopping-cart-row__price">
                <?=$amount ?> x <?=$product->getPriceHtml()?>
            </div>
            <div class="shopping-cart-row__price-total">
                <?= (new \Utils\_Price($product->getPrice() * $amount))->getHtml()?>
            </div>
        </div>
    </div>



</div>
