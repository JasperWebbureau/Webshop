<?php
?>
<section class="webshop-admin-products">
    <grid>
        <div style="--cw:12;--cw-sm:12;--cw-xs:12">
            <div class="webshop-admin-products__header">
                <h1><?=t('webshop_admin_products_overview_title', 'Producten')?></h1>
                <div class="webshop-admin-products__actions">
                    <a class="button button-primary" href="<?=__DOMAIN__?>/Flexgrid/WebshopAdmin/newProduct">
                        <i class="fas fa-plus"></i> <?=t('webshop_admin_product_new', 'Nieuw product')?>
                    </a>
                    <a class="button button-outline" href="<?=__DOMAIN__?>/Flexgrid/WebshopAdmin/dashboard">
                        <i class="fas fa-arrow-left"></i> <?=t('webshop_admin_back_dashboard', 'Terug naar dashboard')?>
                    </a>
                </div>
            </div>
        </div>

        <div class="panel" style="--cw:12;--cw-sm:12;--cw-xs:12">
            <div class="panel__body">
                <form class="webshop-admin-products__filters" method="get" action="<?=__DOMAIN__?>/Flexgrid/WebshopAdmin/products">
                    <input
                        type="search"
                        name="q"
                        value="<?=htmlspecialchars((string)($filters['q'] ?? ''), ENT_QUOTES, 'UTF-8')?>"
                        placeholder="<?=t('webshop_admin_products_search_placeholder', 'Zoek op product, SKU of omschrijving')?>"
                    >
                    <select name="main_group_id">
                        <?php foreach ($mainGroupOptions as $value => $label) { ?>
                            <option value="<?=(int)$value?>" <?=((int)($filters['main_group_id'] ?? 0) === (int)$value) ? 'selected' : ''?>><?=htmlspecialchars((string)$label, ENT_QUOTES, 'UTF-8')?></option>
                        <?php } ?>
                    </select>
                    <select name="group_id">
                        <?php foreach ($groupOptions as $value => $label) { ?>
                            <option value="<?=(int)$value?>" <?=((int)($filters['group_id'] ?? 0) === (int)$value) ? 'selected' : ''?>><?=htmlspecialchars((string)$label, ENT_QUOTES, 'UTF-8')?></option>
                        <?php } ?>
                    </select>
                    <select name="status">
                        <option value=""><?=t('webshop_admin_products_all_statuses', 'Alle statussen')?></option>
                        <?php foreach ($statusOptions as $value => $label) { ?>
                            <option value="<?=$value?>" <?=($filters['status'] ?? '') === $value ? 'selected' : ''?>><?=$label?></option>
                        <?php } ?>
                    </select>
                    <select name="color">
                        <?php foreach ($colorOptions as $value => $label) { ?>
                            <option value="<?=htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8')?>" <?=($filters['color'] ?? '') === (string)$value ? 'selected' : ''?>><?=htmlspecialchars((string)$label, ENT_QUOTES, 'UTF-8')?></option>
                        <?php } ?>
                    </select>
                    <select name="size">
                        <?php foreach ($sizeOptions as $value => $label) { ?>
                            <option value="<?=htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8')?>" <?=($filters['size'] ?? '') === (string)$value ? 'selected' : ''?>><?=htmlspecialchars((string)$label, ENT_QUOTES, 'UTF-8')?></option>
                        <?php } ?>
                    </select>
                    <select name="active">
                        <?php foreach ($activeOptions as $value => $label) { ?>
                            <option value="<?=$value?>" <?=($filters['active'] ?? '') === (string)$value ? 'selected' : ''?>><?=$label?></option>
                        <?php } ?>
                    </select>
                    <button class="button button-primary button-small" type="submit">
                        <?=t('webshop_admin_filter_apply', 'Filteren')?>
                    </button>
                    <?php if (trim((string)($filters['q'] ?? '')) !== '' || (int)($filters['main_group_id'] ?? 0) > 0 || (int)($filters['group_id'] ?? 0) > 0 || trim((string)($filters['status'] ?? '')) !== '' || trim((string)($filters['active'] ?? '')) !== '' || trim((string)($filters['color'] ?? '')) !== '' || trim((string)($filters['size'] ?? '')) !== '') { ?>
                        <a class="button button-outline button-small" href="<?=__DOMAIN__?>/Flexgrid/WebshopAdmin/products">
                            <?=t('webshop_admin_filter_reset', 'Reset')?>
                        </a>
                    <?php } ?>
                </form>

                <div class="webshop-admin-products__table">
                    <?php foreach ($products as $product) { ?>
                        <article class="webshop-admin-products__row">
                            <div>
                                <strong><?=htmlspecialchars((string)$product->getTitle(), ENT_QUOTES, 'UTF-8')?></strong>
                                <span><?=htmlspecialchars((string)$product->getSku(), ENT_QUOTES, 'UTF-8')?></span>
                                <?php if (method_exists($product, 'getLabel') && trim((string)$product->getLabel()) !== '') { ?>
                                    <span><?=htmlspecialchars((string)$product->getLabel(), ENT_QUOTES, 'UTF-8')?></span>
                                <?php } ?>
                                <?php if (trim((string)$product->getColor()) !== '' || trim((string)$product->getSize()) !== '') { ?>
                                    <span>
                                        <?=htmlspecialchars(trim((string)$product->getColor() . ' ' . (string)$product->getSize()), ENT_QUOTES, 'UTF-8')?>
                                    </span>
                                <?php } ?>
                            </div>
                            <div><?=htmlspecialchars((string)$product->getWebshopProductGroupValue(), ENT_QUOTES, 'UTF-8')?></div>
                            <div>&euro; <?=number_format((float)$product->getPrice(), 2, ',', '.')?></div>
                            <div><?=((int)$product->getTrackStock() === 1) ? (int)$product->getStock() : t('webshop_admin_product_stock_not_tracked', 'Niet bijgehouden')?></div>
                            <div>
                                <span class="webshop-admin-products__status webshop-admin-products__status--<?=htmlspecialchars((string)$product->getStatus(), ENT_QUOTES, 'UTF-8')?>">
                                    <?=htmlspecialchars((string)($statusOptions[$product->getStatus()] ?? $product->getStatus()), ENT_QUOTES, 'UTF-8')?>
                                </span>
                                <span><?=((int)$product->getIsActive() === 1) ? t('webshop_admin_active_yes', 'Actief') : t('webshop_admin_active_no', 'Niet actief')?></span>
                            </div>
                            <a class="button button-small" href="<?=__DOMAIN__?>/Flexgrid/WebshopAdmin/productDetail/<?=(int)$product->getId()?>">
                                <?=t('webshop_admin_product_open', 'Open')?>
                            </a>
                        </article>
                    <?php } ?>
                    <?php if (empty($products)) { ?>
                        <p><?=t('webshop_admin_products_empty', 'Geen producten gevonden.')?></p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </grid>
</section>
