<?php
/**
 * @var $product \Flexgrid\Modules\Webshop\Entity\WebshopProduct
 */

use Flexgrid\Event\AjaxEvent;
use Flexgrid\Modules\Webshop\Controller\WebshopController;
use Flexgrid\Modules\Webshop\Service\FavoriteService;

$event = new AjaxEvent(WebshopController::class, 'toggleFavorite');
$event->setMinimumAccessLevel(0);
$isFavorite = (new FavoriteService())->hasProduct((int)$product->getId());
$label = $isFavorite
    ? t('webshop_product_card_favorite_saved', 'Opgeslagen als favoriet')
    : t('webshop_product_card_favorite', 'Bewaar als favoriet');
?>
<div
    class="webshop-product-card2__favorite <?=$isFavorite ? 'is-favorite' : ''?>"
    ajax="true"
    action="<?=$event->getName()?>"
    product_id="<?=(int)$product->getId()?>"
    data-webshop-product-card2-favorite="<?=(int)$product->getId()?>"
    role="button"
    tabindex="0"
    aria-label="<?=htmlspecialchars($label, ENT_QUOTES, 'UTF-8')?>"
    aria-pressed="<?=$isFavorite ? 'true' : 'false'?>"
>
    <i class="<?=$isFavorite ? 'fas' : 'far'?> fa-heart" aria-hidden="true"></i>
</div>
