<?php
use Flexgrid\Form\AjaxField\AjaxField;

$field = static function ($name, $label, $type = null, array $options = []) use ($product) {
    $field = new AjaxField($product, (int)$product->getId(), $name);
    $field->setLabel($label);
    $field->setWidth(12);

    if (!empty($options)) {
        $field->setOptions($options);
    } elseif ($type !== null && $type !== '') {
        $field->setFieldType($type);
    }

    if ($type === 'textarea' || $type === 'html') {
        $field->setHeight(3);
    }

    return '<div class="webshop-admin-product-edit__field">' . $field . '</div>';
};

$previewUrl = '';
if ((int)$productGridPageId > 0) {
    try {
        $previewUrl = (string)$product->getDetailUrl((int)$productGridPageId);
    } catch (\Throwable $exception) {
        $previewUrl = '';
    }
}
?>
<section class="webshop-admin-product-edit">
    <div class="webshop-admin-products__header">
        <h1><?=htmlspecialchars((string)$product->getTitle(), ENT_QUOTES, 'UTF-8')?></h1>
        <div class="webshop-admin-products__actions">
            <?php if ($previewUrl !== '') { ?>
                <a class="button button-publish" href="<?=htmlspecialchars($previewUrl, ENT_QUOTES, 'UTF-8')?>" target="_blank" rel="noopener">
                    <i class="fas fa-external-link-alt"></i> <?=t('webshop_admin_product_preview', 'Open product')?>
                </a>
            <?php } ?>
            <a class="button button-small" href="<?=__DOMAIN__?>/Flexgrid/entityeditor/list/WebshopProduct/<?=(int)$product->getId()?>">
                <i class="fas fa-tools"></i> <?=t('webshop_admin_product_full_editor', 'Volledige editor')?>
            </a>
            <a class="button button-outline" href="<?=__DOMAIN__?>/Flexgrid/WebshopAdmin/products">
                <i class="fas fa-arrow-left"></i> <?=t('webshop_admin_back_products', 'Terug naar producten')?>
            </a>
        </div>
    </div>

    <grid>
        <div class="panel" style="--cw:6;--cw-sm:12;--cw-xs:12">
            <div class="panel__header"><h3><?=t('webshop_admin_product_base', 'Basis')?></h3></div>
            <div class="panel__body webshop-admin-product-edit__grid">
                <?=$field('title', 'Productnaam')?>
                <?=$field('sku', 'SKU')?>
                <?php if (method_exists($product, 'getLabel')) { ?>
                    <?=$field('label', 'Label')?>
                <?php } ?>
                <?=$field('groupId', 'Productgroep', null, $groupOptions)?>
                <?=$field('manufacturer', 'Fabrikant/merk')?>
                <?=$field('color', 'Kleur')?>
                <?=$field('size', 'Maat')?>
                <?=$field('status', 'Status', null, $statusOptions)?>
                <?=$field('isActive', 'Actief', null, ['0' => 'Nee', '1' => 'Ja'])?>
            </div>
        </div>

        <div class="panel" style="--cw:6;--cw-sm:12;--cw-xs:12">
            <div class="panel__header"><h3><?=t('webshop_admin_product_commerce', 'Prijs en voorraad')?></h3></div>
            <div class="panel__body webshop-admin-product-edit__grid">
                <?=$field('price', 'Prijs', 'monetary')?>
                <?=$field('purchasePrice', 'Inkoopprijs', 'monetary')?>
                <?=$field('salePrice', 'Actieprijs', 'monetary')?>
                <?=$field('taxRateId', 'BTW tarief', null, $taxRateOptions)?>
                <?=$field('taxRate', 'Los BTW percentage')?>
                <?=$field('trackStock', 'Voorraad bijhouden', null, ['0' => 'Nee', '1' => 'Ja'])?>
                <?=$field('stock', 'Voorraad')?>
            </div>
        </div>

        <div class="panel" style="--cw:12;--cw-sm:12;--cw-xs:12">
            <div class="panel__header"><h3><?=t('webshop_admin_product_text', 'Teksten')?></h3></div>
            <div class="panel__body webshop-admin-product-edit__grid webshop-admin-product-edit__grid--wide">
                <?=$field('shortDescription', 'Korte omschrijving', 'textarea')?>
                <?=$field('description', 'Omschrijving', 'html')?>
            </div>
        </div>

        <div class="panel" style="--cw:12;--cw-sm:12;--cw-xs:12">
            <div class="panel__header"><h3><?=t('webshop_admin_product_highlight', 'Highlight')?></h3></div>
            <div class="panel__body webshop-admin-product-edit__grid webshop-admin-product-edit__grid--wide">
                <?=$field('highlightImage', 'Highlight afbeelding', 'image')?>
                <?=$field('highlightText', 'Highlight tekst', 'html')?>
            </div>
        </div>

        <div class="panel" style="--cw:12;--cw-sm:12;--cw-xs:12">
            <div class="panel__header">
                <h3><?=t('webshop_admin_product_variants', 'Gekoppelde producten')?></h3>
            </div>
            <div class="panel__body webshop-admin-product-edit__grid webshop-admin-product-edit__grid--wide">
                <?=$field('linkedProducts', 'Gekoppelde producten')?>
            </div>
        </div>
    </grid>
</section>
