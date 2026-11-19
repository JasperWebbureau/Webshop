<?php
use Flexgrid\Form\Input\SelectInput;
use Flexgrid\Response\PageResponse;
$recordCount = (int)($pagination['records'] ?? 0);
$entityName = $pagination['entityName'] ?? '';
$selectOrder = null;
if (!empty($pagination['sortOptions']) && $entityName !== '') {
    $selectOrder = new SelectInput('sortorder[' . $entityName . ']');
    $selectOrder->setOptions($pagination['sortOptions']);
    $selectOrder->setValue($pagination['order'] ?? 'title:ASC');
    $selectOrder->getInputElement()->addClass('order-options');
}
PageResponse::addReplace('-product-count-', $recordCount);
$_REQUEST['render_filter_toolbar_as_replace'] = true;
?>
<grid class="grid-toolbar">
    <div class="grid-toolbar__summary" style="--cw:6;--cw-xs:12">
        <p><?=sprintf(t('webshop_product_count', '%s producten'), $recordCount)?></p>
        <button type="button" class="button button-ghost toggle-filters"><i class="fas fa-sliders-h" aria-hidden="true"></i> <?=t('webshop_filters', 'Filters')?></button>
    </div>
    <div class="grid-toolbar-sort" style="--cw:6;--cw-xs:12">
        <?php if ($selectOrder) { ?><label><?=t('sort_by', 'Sorteer op')?> <?=$selectOrder?></label><?php } ?>
    </div>
    <div class="grid-toolbar__bottom" style="--cw:12">-filter-toolbar-</div>
</grid>
