<?php
/**
* @var \App\Entity\Product $product
*/
$amount = $list['amount'];
$changeAmount = (new \Event\AjaxEvent('App\Webshop\Cart\Cart', 'addArticle' ));
$removeProduct = (new \Event\AjaxEvent('App\Webshop\Cart\Cart', 'removeProduct' ));
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
        <div class="row vcenter g15">
            <div class="shopping-cart-row__price">
                <?=$amount ?> x <?=$product->getPriceHtml()?>
            </div>

            <div class="shopping-cart-row__edit">
                <form  name="cart" container="wrapper" action="<?=$changeAmount->getName()?>" id="<?=$product->getId()?>" ajax="true">
                    <input type="number" value="<?=$amount?>" name="cart[amount]" onchange="$(this).submit()">
                    <input type="hidden" value="<?=$product->getId()?>" name="cart[id]">
                </form>
            </div>

            <div class="shopping-cart-row__price-total">
                <?= (new \Utils\_Price($product->getPrice() * $amount))->getHtml()?>
            </div>
            <div class="shopping-cart-row__delete">
                <div class="button button-icon" ajax="true" container="wrapper" action="<?=$removeProduct->getName()?>" id="<?=$product->getId()?>">
                    <i class="fas fa-trash"></i>
                </div>


            </div>


        </div>

    </div>



</div>
