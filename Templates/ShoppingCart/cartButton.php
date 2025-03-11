<?php
$cart = App\Webshop\Cart\Cart::getInstance();
$productsList = $cart->getProducts();

$total = 0;
\Response\PageResponse::addAsset('App/Webshop/Templates/ShoppingCart/Css/cartButton.scss');
\Response\PageResponse::addAsset('Templates/Webshop/ShoppingCart/ShoppingCartRow/Css/CartRowSimple.scss');

if($_REQUEST['ajax'] == true){

    $showClass = 'show';
}else{  ?>
    <div class="shopping-cart-small-wrapper">
<?php } ?>

<div class="shopping-cart shopping-cart--small <?=$showClass?>">
    <div class="button button-icon">
        <i class="fas fa-shopping-cart"></i>
    </div>
    <div class="card shopping-cart__content ">
        <?php if(empty($productsList)) { ?>
            <div class="">
                Uw winkel wagen is nog leeg
            </div>
        <?php }else { ?>
        <div class="card__content">
            <?php


            $productRepository = new \App\Webshop\Repository\WebshopProductRepository();
            foreach ($productsList as $productId=>$list){

                $product = $productRepository->findById($productId);
                $total += $product->getPrice() * $list['amount'];
                echo new \Response\TemplateResponse('App/Webshop/Templates/ShoppingCart/ShoppingCartRow/CartRowSimple.php', ['list'=>$list, 'product'=>$product]);
            }



            ?>
        </div>
        <div class="card__total">
            <div class="row">
                <?= (new \Utils\_Price($total))->getHtml()?>
            </div>

        </div>
        <div class="card__footer">
            <a href="<?=__DOMAIN__ . '/'. $cart->getOrderPage()->getUrl()?>" class="button button-secondary">Bestellen</a>
            <a href="<?=__DOMAIN__ . '/'. $cart->getCartPage()->getUrl()?>" class="button button-primary">Winkel Wagen</a>
        </div>
        <?php } ?>
    </div>

</div>

<?php if($_REQUEST['ajax'] == true){  ?>
    <script>

        $(function(){
            setTimeout(function(){
                $('.shopping-cart').removeClass('show');
            }, 1500)
        })
    </script>
<?php
}else{
?>
    </div>
<?php }

