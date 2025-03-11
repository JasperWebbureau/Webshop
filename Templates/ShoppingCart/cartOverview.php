<?php
$cart = App\Webshop\Cart\Cart::getInstance();
$delivery = new \App\Webshop\Delivery\Delivery();
$currentDelivery = $delivery->getSelected();

/**
 * @var $orderPage \Flexgrid\Entity\Page
 */
if( ! __AJAX__){
    ?>
    <section class="cart--wrapper">
<?php
}
?>
<grid>
    <div class="card card--shopping-cart-overview" style="-zz-cw:8">
        <div class="card__header">
            <h2>Winkel wagen</h2>
        </div>
        <div class="card__content">
            <?php
            $productsList = $cart->getProducts();

            $productRepository = new \App\Webshop\Repository\WebshopProductRepository();
            foreach ($productsList as $productId=>$list){

                $product = $productRepository->findById($productId);
                $total += $product->getPrice() * $list['amount'];
                if($editable== true){
                    echo new \Response\TemplateResponse('App/Webshop/Templates/ShoppingCart/ShoppingCartRow/CartRowEditable.php', ['list'=>$list, 'product'=>$product]);
                }else{
                    echo new \Response\TemplateResponse('App/Webshop/Templates/ShoppingCart/ShoppingCartRow/CartRowSimple.php', ['list'=>$list, 'product'=>$product]);

                }
            }

            $deliveryPrice = new \Utils\_Price($currentDelivery->getPrice());
            $total += $currentDelivery->getPrice();
           $price = new \Utils\_Price($total);
            ?>
        </div>
        <div class="card__footer">
            <div class="row space-between">
                <div class="">
                    Verzend kosten
                </div>
                <div class="">
                    <?=$deliveryPrice->getHtml()?>
                </div>
            </div>
            <div class="row space-between">
                <div class="">
                    Btw
                </div>
                <div class="">
                    <?=$price->getHtml(21,true)?>
                </div>
            </div>
            <div class="row space-between">
                <div class="">
                    Totaal
                </div>
                <div class="">
                    <?=$price->getHtml()?>
                </div>
            </div>
            <?php



            ?>

        </div>
    </div>
</grid>
<?php if( ! __AJAX__){  ?>
    </section>
<?php
}
?>