<?php
$selectOrder = null;
$entityName = $pagination['entityName'] ?? '';
if(isset($pagination['sortOptions']) && is_array($pagination['sortOptions'])){

    $pagination['sortOptions'] = array_merge([''=>'Sorteren'],  $pagination['sortOptions']);

    $selectOrder = new \Flexgrid\Form\Input\SelectInput('sortorder['.$entityName .']');
    if(isset($_REQUEST['sortorder'][$entityName]) && $_REQUEST['sortorder'][$entityName] != null){
        $selectOrder->setValue($_REQUEST['sortorder'][$entityName]);
    }
    $selectOrder->setOptions($pagination['sortOptions']);
    $selectOrder->getInputElement()->addClass('order-options');//
    /// $selectOrder->getInputElement()->setAttribute('onchange', 'Filters.fireAll()');
}
if($selectOrder != null){
    $selectOrder->setValue($pagination['order'] ?? '');
}
$detail = \Flexgrid\Flexgrid::getApp()->getPath()->getEntity();
$groupName = '';
$groupImage = \Flexgrid\Utils\Files\ImageFile::getImageFile(110);// 'https://demo.webbureau.nu/centralrental2025/Files/allcars.png';
$imageFile = $groupImage;//new \Flexgrid\Utils\Files\ImageFile('https://demo.webbureau.nu/centralrental2025/Files/allcars.png');
$groupImage = $imageFile->getResizeUrl(80, 50);
$resultTitle = 'Producten';
$recordCount = (int)($pagination['records'] ?? 0);
if($recordCount > 1 || $recordCount == 0){
    $resultTitle = 'Producten';
}

if($detail != null && $detail->getId() > 0){
    if(get_class($detail) == 'App\Occasion\Entity\OccasionType'  ){
        $groupName = $detail->getTitle();
        $groupImage = $detail->getImage()->getResizeUrl(80, 50);
        $resultTitle ='';
    };
}

$snippetController = (new \App\Controller\HtmlSnippetController());
$snippetController->addReplace('-current-group-image-', $groupImage);
$snippetController->addReplace('-current-group-', $groupName);


\Flexgrid\Response\PageResponse::addReplace('-product-count-', $recordCount);
$_REQUEST['render_filter_toolbar_as_replace'] =true;
?>

<grid class="grid-toolbar">

    <div class="grid-toolbar__summary" style="--cw:6">
        <div class="current-results">
            <h3><span class="current-results__number">    -product-count- -current-group- <?=$resultTitle?></h3>
        </div>
        <div class="grid-toolbar__product-count">

            <div class="button button-ghost toggle-filters">
                <i class="fa-solid fa-sliders"></i> Filters
            </div>

        </div>
    </div>

    <div class="grid-toolbar-sort" style="--cw:6">
        <div class="toolbar-options__filter-results hidden-xs">

        </div>
        <div class="grid-toolbar-sort__block">
            <?php if($selectOrder != null){ ?>
                <span><?= t('sort_by', 'Sorteer op') ?> </span>
                <div class="toolbar-options__sort">
                    <?=$selectOrder?>
                </div>
            <?php } ?>
        </div>

    </div>


    <div class="grid-toolbar__bottom" style="--cw:12">
        -filter-toolbar-
    </div>

</grid>
