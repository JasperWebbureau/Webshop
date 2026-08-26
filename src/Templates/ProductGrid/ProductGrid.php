<?php

use Flexgrid\Response\PageResponse;

$paginationHtml = '';
if( isset($pagination) && ! empty($pagination) ){
    $paginationHtml = (string)new \Flexgrid\Response\TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/ProductGrid/Toolbar/Toolbar.php', ['pagination' => $pagination]);
}

PageResponse::addReplace('-webshop-product-toolbar-', $paginationHtml);
?>

<grid class="webshop-product-grid">
    <?php if (empty($entities)) { ?>
        <div class="webshop-product-grid__empty" style="--cw:12;--cw-sm:12;--cw-xs:12">
            <i class="fas fa-box-open"></i>
            <h2><?=t('webshop_product_grid_empty_title', 'Geen producten gevonden')?></h2>
            <p><?=t('webshop_product_grid_empty_text', 'Er zijn op dit moment geen producten beschikbaar.')?></p>
        </div>
    <?php } ?>
    <?php
    $shownProductClusters = [];
    foreach ($entities as $entity) {
        $clusterKey = method_exists($entity, 'getVariantClusterKey') ? (string)$entity->getVariantClusterKey() : (string)$entity->getId();
        if ($clusterKey !== '' && isset($shownProductClusters[$clusterKey])) {
            continue;
        }
        $shownProductClusters[$clusterKey] = true;
        ?>
        <?=new \Flexgrid\Response\TemplateResponse($card, [
            'entity' => $entity,
            'pageId' => $pageId,
            'cardWidth' => $cardWidth,
            'parentWidth' => $parentWidth,
        ])?>
    <?php } ?>
</grid>
