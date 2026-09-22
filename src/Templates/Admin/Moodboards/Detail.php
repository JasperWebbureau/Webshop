<?php
/**
 * @var $moodboard \Flexgrid\Modules\Webshop\Entity\WebshopMoodboard
 * @var $items \Flexgrid\Modules\Webshop\Entity\WebshopMoodboardItem[]
 * @var $productOptions array
 * @var $productGridPageId int
 */

use Flexgrid\Form\AjaxField\AjaxField;

$field = static function ($name, $label, $type = null, array $options = []) use ($moodboard) {
    $field = new AjaxField($moodboard, (int)$moodboard->getId(), $name);
    $field->setLabel($label);
    $field->setWidth(12);

    if (!empty($options)) {
        $field->setOptions($options);
    } elseif ($type !== null && $type !== '') {
        $field->setFieldType($type);
    }

    if ($type === 'textarea') {
        $field->setHeight(3);
    }

    return '<div class="webshop-admin-moodboard-edit__field">' . $field . '</div>';
};

$previewItems = [];
foreach ($items as $item) {
    $product = $item->getWebshopProductParent();
    if (!$product || (int)$product->getId() <= 0) {
        continue;
    }

    $previewItems[] = [
        'item' => $item,
        'product' => $product,
    ];
}
?>
<section class="webshop-admin-moodboard-edit" data-webshop-admin-moodboard>
    <div class="webshop-admin-moodboards__header">
        <h1><?=htmlspecialchars((string)($moodboard->getTitle() ?: t('webshop_admin_moodboard_new', 'Nieuw inspiratieblok')), ENT_QUOTES, 'UTF-8')?></h1>
        <div class="webshop-admin-moodboards__actions">
            <?php if ((int)$moodboard->getId() > 0) { ?>
                <a class="button button-small" href="<?=__DOMAIN__?>/Flexgrid/entityeditor/list/WebshopMoodboard/<?=(int)$moodboard->getId()?>">
                    <i class="fas fa-tools"></i> <?=t('webshop_admin_moodboard_full_editor', 'Volledige editor')?>
                </a>
            <?php } ?>
            <a class="button button-outline" href="<?=__DOMAIN__?>/Flexgrid/WebshopAdmin/moodboards">
                <i class="fas fa-arrow-left"></i> <?=t('webshop_admin_back_moodboards', 'Terug naar inspiratieblokken')?>
            </a>
        </div>
    </div>

    <form method="post" action="<?=__DOMAIN__?>/Flexgrid/WebshopAdmin/saveMoodboard">
        <input type="hidden" name="id" value="<?=(int)$moodboard->getId()?>">

        <grid>
            <div class="panel" style="--cw:4;--cw-sm:12;--cw-xs:12">
                <div class="panel__header"><h3><?=t('webshop_admin_moodboard_base', 'Basis')?></h3></div>
                <div class="panel__body webshop-admin-moodboard-edit__fields">
                    <label>
                        <span><?=t('webshop_admin_moodboard_title', 'Titel')?></span>
                        <input type="text" name="title" value="<?=htmlspecialchars((string)$moodboard->getTitle(), ENT_QUOTES, 'UTF-8')?>" required>
                    </label>
                    <label>
                        <span><?=t('webshop_admin_moodboard_eyebrow', 'Eyebrow')?></span>
                        <input type="text" name="eyebrow" value="<?=htmlspecialchars((string)$moodboard->getEyebrow(), ENT_QUOTES, 'UTF-8')?>">
                    </label>
                    <label>
                        <span><?=t('webshop_admin_moodboard_intro', 'Intro')?></span>
                        <textarea name="intro" rows="4"><?=htmlspecialchars((string)$moodboard->getIntro(), ENT_QUOTES, 'UTF-8')?></textarea>
                    </label>
                    <label>
                        <span><?=t('webshop_admin_moodboard_button_text', 'Knoptekst')?></span>
                        <input type="text" name="button_text" value="<?=htmlspecialchars((string)$moodboard->getButtonText(), ENT_QUOTES, 'UTF-8')?>">
                    </label>
                    <label class="webshop-admin-moodboard-edit__checkbox">
                        <input type="checkbox" name="is_active" value="1" <?=((int)$moodboard->getIsActive() !== 0) ? 'checked' : ''?>>
                        <span><?=t('webshop_admin_moodboard_active', 'Actief')?></span>
                    </label>

                    <?php if ((int)$moodboard->getId() > 0) { ?>
                        <?=$field('image', 'Hoofdafbeelding', 'image')?>
                    <?php } else { ?>
                        <p><?=t('webshop_admin_moodboard_save_first', 'Sla het inspiratieblok eerst op om de hoofdafbeelding te kiezen.')?></p>
                    <?php } ?>
                </div>
            </div>

            <div class="panel" style="--cw:8;--cw-sm:12;--cw-xs:12">
                <div class="panel__header">
                    <h3><?=t('webshop_admin_moodboard_hotspots', 'Hotspots')?></h3>
                    <button class="button button-small button-secondary" type="button" data-webshop-moodboard-add-row>
                        <i class="fas fa-plus"></i> <?=t('webshop_admin_moodboard_add_product', 'Product toevoegen')?>
                    </button>
                </div>
                <div class="panel__body">
                    <div class="webshop-admin-moodboard-edit__workspace">
                        <div class="webshop-admin-moodboard-edit__preview" data-webshop-moodboard-preview>
                            <?php if ((int)$moodboard->getId() > 0) { ?>
                                <img src="<?=$moodboard->getImage()->getResizeUrl(1200, 760, 1)?>" alt="">
                            <?php } ?>
                            <?php foreach ($previewItems as $index => $entry) {
                                $item = $entry['item'];
                                ?>
                                <button
                                    type="button"
                                    class="webshop-admin-moodboard-edit__pin <?=$index === 0 ? 'is-active' : ''?>"
                                    style="--pin-x: <?=number_format((float)$item->getPositionX(), 2, '.', '')?>%; --pin-y: <?=number_format((float)$item->getPositionY(), 2, '.', '')?>%;"
                                    data-webshop-moodboard-pin="<?=(int)$item->getId()?>"
                                >
                                    <?=str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT)?>
                                </button>
                            <?php } ?>
                        </div>

                        <div class="webshop-admin-moodboard-edit__items" data-webshop-moodboard-items>
                            <?php foreach ($items as $index => $item) { ?>
                                <div class="webshop-admin-moodboard-edit__item <?=$index === 0 ? 'is-active' : ''?>" data-webshop-moodboard-row="<?=(int)$item->getId()?>">
                                    <input type="hidden" name="items[<?=$index?>][id]" value="<?=(int)$item->getId()?>">
                                    <label>
                                        <span><?=t('webshop_admin_moodboard_product', 'Product')?></span>
                                        <select name="items[<?=$index?>][product_id]">
                                            <?php foreach ($productOptions as $value => $label) { ?>
                                                <option value="<?=(int)$value?>" <?=((int)$item->getProductId() === (int)$value) ? 'selected' : ''?>><?=htmlspecialchars((string)$label, ENT_QUOTES, 'UTF-8')?></option>
                                            <?php } ?>
                                        </select>
                                    </label>
                                    <label>
                                        <span>X %</span>
                                        <input type="number" min="0" max="100" step="0.1" name="items[<?=$index?>][position_x]" value="<?=htmlspecialchars((string)$item->getPositionX(), ENT_QUOTES, 'UTF-8')?>" data-webshop-moodboard-x>
                                    </label>
                                    <label>
                                        <span>Y %</span>
                                        <input type="number" min="0" max="100" step="0.1" name="items[<?=$index?>][position_y]" value="<?=htmlspecialchars((string)$item->getPositionY(), ENT_QUOTES, 'UTF-8')?>" data-webshop-moodboard-y>
                                    </label>
                                    <label>
                                        <span><?=t('webshop_admin_order', 'Volgorde')?></span>
                                        <input type="number" name="items[<?=$index?>][order]" value="<?=htmlspecialchars((string)$item->getOrder(), ENT_QUOTES, 'UTF-8')?>">
                                    </label>
                                    <label class="webshop-admin-moodboard-edit__delete">
                                        <input type="checkbox" name="items[<?=$index?>][delete]" value="1">
                                        <span><?=t('webshop_admin_delete', 'Verwijderen')?></span>
                                    </label>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="panel__footer">
                    <button class="button button-publish" type="submit">
                        <i class="fas fa-save"></i> <?=t('webshop_admin_moodboard_save', 'Inspiratieblok opslaan')?>
                    </button>
                    <?php if ((int)$moodboard->getId() > 0) { ?>
                        <button class="button button-delete" type="submit" formaction="<?=__DOMAIN__?>/Flexgrid/WebshopAdmin/deleteMoodboard">
                            <i class="fas fa-trash"></i> <?=t('webshop_admin_moodboard_delete', 'Verwijderen')?>
                        </button>
                    <?php } ?>
                </div>
            </div>
        </grid>
    </form>

    <template data-webshop-moodboard-row-template>
        <div class="webshop-admin-moodboard-edit__item is-active" data-webshop-moodboard-row="new">
            <input type="hidden" data-name="id" value="0">
            <label>
                <span><?=t('webshop_admin_moodboard_product', 'Product')?></span>
                <select data-name="product_id">
                    <?php foreach ($productOptions as $value => $label) { ?>
                        <option value="<?=(int)$value?>"><?=htmlspecialchars((string)$label, ENT_QUOTES, 'UTF-8')?></option>
                    <?php } ?>
                </select>
            </label>
            <label>
                <span>X %</span>
                <input type="number" min="0" max="100" step="0.1" data-name="position_x" value="50" data-webshop-moodboard-x>
            </label>
            <label>
                <span>Y %</span>
                <input type="number" min="0" max="100" step="0.1" data-name="position_y" value="50" data-webshop-moodboard-y>
            </label>
            <label>
                <span><?=t('webshop_admin_order', 'Volgorde')?></span>
                <input type="number" data-name="order" value="0">
            </label>
            <label class="webshop-admin-moodboard-edit__delete">
                <input type="checkbox" data-name="delete" value="1">
                <span><?=t('webshop_admin_delete', 'Verwijderen')?></span>
            </label>
        </div>
    </template>
</section>
