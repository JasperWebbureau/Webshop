<?php
/**
 * @var $entity \Flexgrid\Modules\Webshop\Entity\WebshopProductMainGroup|null
 * @var $productGroupGrid \Flexgrid\Response\TemplateResponse
 * @var $allProductsUrl string
 * @var $showAllProducts bool
 * @var $productGrid \Flexgrid\Response\TemplateResponse|null
 * @var $productPagination \Flexgrid\Response\TemplateResponse|null
 */
if (!$entity) {
    return;
}

$intro = trim((string)$entity->getIntroSentence());
?>
<section class="webshop-main-group-product-groups">
    <?php if ($intro !== '') { ?>
        <h2><?=htmlspecialchars($intro, ENT_QUOTES, 'UTF-8')?></h2>
    <?php } ?>
    <?=$productGroupGrid?>
    <?php if ($showAllProducts) { ?>
        <div class="webshop-main-group-product-groups__all-products">
            <h2><?=htmlspecialchars(t('webshop_main_group_all_products_title', 'Alle producten in deze hoofdgroep'), ENT_QUOTES, 'UTF-8')?></h2>
            <?=$productGrid?>
            <?=$productPagination?>
        </div>
    <?php } elseif ($allProductsUrl !== '') { ?>
        <div class="webshop-main-group-product-groups__action">
            <a class="button button-primary" href="<?=htmlspecialchars($allProductsUrl, ENT_QUOTES, 'UTF-8')?>">
                <?=htmlspecialchars(t('webshop_main_group_view_all_products', 'Bekijk alle producten in deze groep'), ENT_QUOTES, 'UTF-8')?>
            </a>
        </div>
    <?php } ?>
</section>
