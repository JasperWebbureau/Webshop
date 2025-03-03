<?php
$cart = App\Webshop\Cart\Cart::getInstance();

/**
 * @var $orderPage \Flexgrid\Entity\Page
 */
?>
<grid>
    <div class="card card--shopping-cart-overview" style="-zz-cw:8">
        <div class="card__header">
            <h2>Winkel wagen</h2>
        </div>
        <div class="card__content">
            <?php
        $productsList = $cart->getProducts();

            $productRepository = new \App\Repository\ProductRepository();
            foreach ($productsList as $productId=>$list){

                $product = $productRepository->findById($productId);
                $total += $product->getPrice() * $list['amount'];
                echo new \Response\TemplateResponse('App/Webshop/Templates/ShoppingCart/ShoppingCartRow/CartRowSimple.php', ['list'=>$list, 'product'=>$product]);
            }
            ?>
        </div>
        <div class="card__footer">

        </div>
    </div>
</grid>