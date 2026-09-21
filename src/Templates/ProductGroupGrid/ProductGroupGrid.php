<grid class="webshop-product-group-grid">
    <?php
    $count = 0;
    if (empty($entities)) { ?>
        <div class="webshop-product-group-grid__empty" style="--cw:12;--cw-sm:12;--cw-xs:12">
            <i class="fas fa-layer-group"></i>
            <h2><?=t('webshop_product_group_grid_empty_title', 'Geen productgroepen gevonden')?></h2>
            <p><?=t('webshop_product_group_grid_empty_text', 'Er zijn op dit moment geen productgroepen beschikbaar.')?></p>
        </div>
    <?php }

    foreach ($entities as $entity) {
        $count++;
        if ($limit > 0 && $count > $limit) {
            break;
        }
        ?>
        <?=new \Flexgrid\Response\TemplateResponse($card, [
            'entity' => $entity,
            'pageId' => $pageId,
            'cardWidth' => $cardWidth,
            'parentWidth' => $parentWidth,
            'cardIndex' => $count - 1,
        ])?>
    <?php } ?>
</grid>
