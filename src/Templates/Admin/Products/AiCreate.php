<?php
$error = trim((string)($error ?? ''));
$credits = $credits ?? [];
$desiredMarginPercentage = (float)($desiredMarginPercentage ?? 30);
$purchasePrice = (float)($purchasePrice ?? 0);
$useWebSearch = (bool)($useWebSearch ?? false);
$allowGroupCreate = (bool)($allowGroupCreate ?? false);
$allowPurchasePriceEstimate = (bool)($allowPurchasePriceEstimate ?? false);
$allowTitleRewrite = (bool)($allowTitleRewrite ?? false);
$allowVariantDetection = (bool)($allowVariantDetection ?? false);
$minimumDescriptionParagraphs = max(1, min(8, (int)($minimumDescriptionParagraphs ?? 2)));
?>
<section class="webshop-admin-products webshop-admin-product-ai-create">
    <div class="webshop-admin-products__header">
        <h1><?=t('webshop_admin_product_ai_create_title', 'Nieuw product')?></h1>
        <div class="webshop-admin-products__actions">
            <a class="button button-small" href="<?=__DOMAIN__?>/Flexgrid/entityeditor/list/WebshopProduct/0">
                <i class="fas fa-tools"></i> <?=t('webshop_admin_product_full_editor', 'Handmatig product')?>
            </a>
            <a class="button button-outline" href="<?=__DOMAIN__?>/Flexgrid/WebshopAdmin/products">
                <i class="fas fa-arrow-left"></i> <?=t('webshop_admin_back_products', 'Terug naar producten')?>
            </a>
        </div>
    </div>

    <grid>
        <div class="panel webshop-admin-product-ai-create__panel" style="--cw:7;--cw-sm:12;--cw-xs:12">
            <div class="panel__header">
                <h3><?=t('webshop_admin_product_ai_create_panel', 'Product starten met AI')?></h3>
            </div>
            <div class="panel__body">
                <?php if ($error !== '') { ?>
                    <div class="webshop-admin-product-ai-create__message is-error">
                        <?=htmlspecialchars($error, ENT_QUOTES, 'UTF-8')?>
                    </div>
                <?php } ?>

                <form class="webshop-admin-product-ai-create__form" method="post" enctype="multipart/form-data" action="<?=__DOMAIN__?>/Flexgrid/WebshopAdmin/generateProductWithAi">
                    <label>
                        <span><?=t('webshop_admin_product_ai_seed_title', 'Titel of korte omschrijving')?></span>
                        <input
                            type="text"
                            name="title"
                            value="<?=htmlspecialchars((string)($title ?? ''), ENT_QUOTES, 'UTF-8')?>"
                            placeholder="<?=t('webshop_admin_product_ai_seed_title_placeholder', 'Bijv. Linnen kussen olijfgroen')?>">
                    </label>

                    <label>
                        <span><?=t('webshop_admin_product_ai_manufacturer', 'Fabrikant/merk')?></span>
                        <input
                            type="text"
                            name="manufacturer"
                            value="<?=htmlspecialchars((string)($manufacturer ?? ''), ENT_QUOTES, 'UTF-8')?>"
                            placeholder="<?=t('webshop_admin_product_ai_manufacturer_placeholder', 'Optioneel, bijv. Hay of Ferm Living')?>">
                    </label>

                    <label>
                        <span><?=t('webshop_admin_product_ai_purchase_price', 'Inkoopprijs')?></span>
                        <input
                            type="text"
                            name="purchase_price"
                            inputmode="decimal"
                            value="<?=$purchasePrice > 0 ? htmlspecialchars(number_format($purchasePrice, 2, ',', ''), ENT_QUOTES, 'UTF-8') : ''?>"
                            placeholder="<?=t('webshop_admin_product_ai_purchase_price_placeholder', 'Optioneel, bijv. 24,95')?>">
                    </label>

                    <label>
                        <span><?=t('webshop_admin_product_ai_image', 'Productafbeeldingen')?></span>
                        <input type="file" name="image[]" accept="image/*" multiple>
                    </label>

                    <label>
                        <span><?=t('webshop_admin_product_ai_min_paragraphs', 'Minimaal aantal alinea\'s')?></span>
                        <input
                            type="number"
                            name="minimum_description_paragraphs"
                            min="1"
                            max="8"
                            value="<?=(int)$minimumDescriptionParagraphs?>">
                    </label>

                    <label class="webshop-admin-product-ai-create__checkbox">
                        <input type="checkbox" name="use_web_search" value="1" <?=$useWebSearch ? 'checked' : ''?>>
                        <span><?=t('webshop_admin_product_ai_web_search', 'Online zoeken naar marktprijzen en fabrikant/merk')?></span>
                    </label>

                    <label class="webshop-admin-product-ai-create__checkbox">
                        <input type="checkbox" name="allow_group_create" value="1" <?=$allowGroupCreate ? 'checked' : ''?>>
                        <span><?=t('webshop_admin_product_ai_group_create', 'Mag productgroep toevoegen als niets matcht')?></span>
                    </label>

                    <label class="webshop-admin-product-ai-create__checkbox">
                        <input type="checkbox" name="allow_purchase_price_estimate" value="1" <?=$allowPurchasePriceEstimate ? 'checked' : ''?>>
                        <span><?=t('webshop_admin_product_ai_purchase_estimate', 'Mag inkoopprijs bepalen als deze leeg is')?></span>
                    </label>

                    <label class="webshop-admin-product-ai-create__checkbox">
                        <input type="checkbox" name="allow_title_rewrite" value="1" <?=$allowTitleRewrite ? 'checked' : ''?>>
                        <span><?=t('webshop_admin_product_ai_title_rewrite', 'Mag titel overschrijven/aanvullen aan de hand van de afbeelding')?></span>
                    </label>

                    <label class="webshop-admin-product-ai-create__checkbox">
                        <input type="checkbox" name="allow_variant_detection" value="1" <?=$allowVariantDetection ? 'checked' : ''?>>
                        <span><?=t('webshop_admin_product_ai_variant_detection', 'Mag kleur/maat varianten herkennen en siblings aanmaken')?></span>
                    </label>

                    <button class="button button-primary" type="submit">
                        <i class="fas fa-magic"></i>
                        <?=t('webshop_admin_product_ai_generate', 'Vul product met AI')?>
                    </button>
                </form>
            </div>
        </div>

        <aside class="panel webshop-admin-product-ai-create__panel" style="--cw:5;--cw-sm:12;--cw-xs:12">
            <div class="panel__header">
                <h3><?=t('webshop_admin_product_ai_create_context', 'Wat wordt gevuld')?></h3>
            </div>
            <div class="panel__body webshop-admin-product-ai-create__context">
                <div>
                    <strong><?=t('webshop_admin_product_ai_context_fields', 'Productvelden')?></strong>
                    <span>titel, SKU, productgroep, fabrikant/merk, prijs, inkoopprijs, teksten, kleur, maat, voorraad en BTW</span>
                </div>
                <div>
                    <strong><?=t('webshop_admin_product_ai_context_margin', 'Marge')?></strong>
                    <span><?=sprintf(t('webshop_admin_product_ai_context_margin_text', 'Bij een ingevulde inkoopprijs wordt de verkoopprijs berekend met %.0f%% marge uit de webshop instellingen.'), $desiredMarginPercentage)?></span>
                </div>
                <div>
                    <strong><?=t('webshop_admin_product_ai_context_web_search', 'Online zoektocht')?></strong>
                    <span><?=t('webshop_admin_product_ai_context_web_search_text', 'Standaard uit. Als je dit aanzet mag AI online zoeken naar vergelijkbare producten en, wanneer leeg gelaten, fabrikant/merk.')?></span>
                </div>
                <div>
                    <strong><?=t('webshop_admin_product_ai_context_groups', 'Productgroepen')?></strong>
                    <span><?=t('webshop_admin_product_ai_context_groups_text', 'Beschikbare productgroep-id\'s en titels worden meegestuurd. Met de extra optie mag AI een nieuwe groep voorstellen als niets past.')?></span>
                </div>
                <div>
                    <strong><?=t('webshop_admin_product_ai_context_description', 'Omschrijving')?></strong>
                    <span><?=sprintf(t('webshop_admin_product_ai_context_description_text', 'AI schrijft minimaal %d alinea\'s in eenvoudige HTML.'), (int)$minimumDescriptionParagraphs)?></span>
                </div>
                <div>
                    <strong><?=t('webshop_admin_product_ai_context_variants', 'Varianten')?></strong>
                    <span><?=t('webshop_admin_product_ai_context_variants_text', 'Met variant-herkenning aan mag AI meerdere producten voorstellen. De module koppelt die producten daarna als siblings.')?></span>
                </div>
                <div>
                    <strong><?=t('webshop_admin_product_ai_context_credits', 'Credits')?></strong>
                    <span><?=t('webshop_admin_product_ai_context_credits_text', 'Bij een succesvolle OpenAI-call worden credits automatisch afgeschreven op basis van het tokengebruik.')?></span>
                </div>
            </div>
        </aside>
    </grid>
</section>
