<?php
/**
 * @var $product \Flexgrid\Modules\Webshop\Entity\WebshopProduct
 * @var $showQuantity bool
 */

use Flexgrid\Event\AjaxEvent;
use Flexgrid\Modules\Webshop\Controller\WebshopProductController;

$showQuantity = $showQuantity ?? true;
$isAvailable = (int)$product->getIsActive() === 1
    && (string)$product->getStatus() === 'published'
    && ((int)$product->getTrackStock() !== 1 || (int)$product->getStock() > 0);

$addToCartEvent = new AjaxEvent(WebshopProductController::class, 'addToCart');
$addToCartEvent->setMinimumAccessLevel(0);
?>
<div class="webshop-add-to-cart">
    <form class="webshop-add-to-cart__form" ajax="true" action="<?=$addToCartEvent->getName()?>">
        <input type="hidden" name="product_id" value="<?=(int)$product->getId()?>">

        <?php if (!empty($showQuantity)) { ?>
            <label class="webshop-add-to-cart__quantity"  style="--cw:8">
                <span><?=t('webshop_add_to_cart_quantity', 'Aantal')?></span>
                <input
                    type="number"
                    name="quantity"
                    value="1"
                    min="1"
                    step="1"
                    <?=$isAvailable ? '' : 'disabled'?>
                >
            </label>
        <?php } else { ?>
            <input type="hidden" name="quantity" value="1">
        <?php } ?>

        <button class="button button-primary webshop-add-to-cart__button"  style="--cw:4" type="submit" use-waiting-icon="true" <?=$isAvailable ? '' : 'disabled'?>>
            <i class="fas fa-shopping-cart"></i>
            <?=$isAvailable ? t('webshop_add_to_cart', 'In winkelwagen') : t('webshop_add_to_cart_unavailable', 'Niet beschikbaar')?>
        </button>
    </form>
    <div class="webshop-cart-feedback" data-webshop-cart-feedback></div>
</div>
