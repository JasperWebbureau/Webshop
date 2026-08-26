<?php
/**
 * @var $product \Flexgrid\Modules\Webshop\Entity\WebshopProduct
 */

use Flexgrid\Event\AjaxEvent;
use Flexgrid\Modules\Webshop\Controller\WebshopController;

$isFavorite = !empty($isFavorite);
$event = new AjaxEvent(WebshopController::class, 'toggleFavorite');
$event->setMinimumAccessLevel(0);
$label = $isFavorite
    ? t('webshop_product_detail_favorite_saved', 'Opgeslagen als favoriet')
    : t('webshop_product_detail_favorite', 'Bewaar als favoriet');
?>
<form class="webshop-product-detail__favorite-form" ajax="true" action="<?=$event->getName()?>" data-webshop-favorite-form="<?=(int)$product->getId()?>">
    <input type="hidden" name="product_id" value="<?=(int)$product->getId()?>">
    <button type="submit" class="webshop-product-detail__favorite <?=$isFavorite ? 'is-favorite' : ''?>" use-waiting-icon="true" aria-pressed="<?=$isFavorite ? 'true' : 'false'?>">
        <i class="<?=$isFavorite ? 'fas' : 'far'?> fa-heart" aria-hidden="true"></i>
        <span><?=$label?></span>
    </button>
</form>
