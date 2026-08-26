<?php
/**
 * @var $entity \Flexgrid\Modules\Webshop\Entity\WebshopProductMainGroup|null
 */
if (!$entity) {
    return;
}

$title = $mode === 'highlight'
    ? t('webshop_main_group_slider_highlight_title', 'Uitgelicht in ' . $entity->getTitle())
    : t('webshop_main_group_slider_new_title', 'Nieuw in ' . $entity->getTitle());
?>
<section class="webshop-main-group-slider">
    <h2><?=htmlspecialchars($title, ENT_QUOTES, 'UTF-8')?></h2>
    <grid class="webshop-main-group-slider__grid">
        <?php foreach ($entities as $product) { ?>
            <?=new \Flexgrid\Response\TemplateResponse($card, [
                'entity' => $product,
                'pageId' => $pageId,
                'cardWidth' => $cardWidth,
                'parentWidth' => $parentWidth,
            ])?>
        <?php } ?>
    </grid>
</section>
