<?php
use Flexgrid\Event\AjaxEvent;
use Flexgrid\Modules\Webshop\Controller\WebshopAdminController;

$saveEvent = new AjaxEvent(WebshopAdminController::class, 'saveSettings');
?>
<section class="webshop-admin-settings">
    <grid>
        <div style="--cw:12;--cw-sm:12;--cw-xs:12">
            <div class="webshop-admin-settings__header">
                <h1><?=t('webshop_admin_settings_title', 'Webshop instellingen')?></h1>
                <a class="button button-outline" href="<?=__DOMAIN__?>/Flexgrid/WebshopAdmin/dashboard">
                    <i class="fas fa-arrow-left"></i> <?=t('webshop_admin_back_dashboard', 'Terug naar dashboard')?>
                </a>
            </div>
        </div>

        <div class="panel" style="--cw:12;--cw-sm:12;--cw-xs:12">
            <div class="panel__body">
                <form ajax="true" action="<?=$saveEvent->getName()?>">
                    <div class="webshop-admin-settings__sections">
                        <?php foreach ($settings as $section) { ?>
                            <section class="webshop-admin-settings__section">
                                <h2><?=htmlspecialchars((string)$section['title'], ENT_QUOTES, 'UTF-8')?></h2>
                                <div class="webshop-admin-settings__fields">
                                    <?php foreach ($section['fields'] as $key => $field) { ?>
                                        <label class="webshop-admin-settings__field webshop-admin-settings__field--<?=$field['type']?>">
                                            <span><?=htmlspecialchars((string)$field['label'], ENT_QUOTES, 'UTF-8')?></span>
                                            <?php if ($field['type'] === 'select') { ?>
                                                <select name="webshop_settings[<?=$key?>]">
                                                    <?php foreach ($field['options'] as $value => $label) { ?>
                                                        <option value="<?=htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8')?>" <?=$field['value'] === $value ? 'selected' : ''?>>
                                                            <?=htmlspecialchars((string)$label, ENT_QUOTES, 'UTF-8')?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            <?php } elseif ($field['type'] === 'checkbox') { ?>
                                                <input type="checkbox" name="webshop_settings[<?=$key?>]" value="1" <?=(int)$field['value'] === 1 ? 'checked' : ''?>>
                                            <?php } else { ?>
                                                <input
                                                    type="<?=$field['type']?>"
                                                    name="webshop_settings[<?=$key?>]"
                                                    value="<?=htmlspecialchars((string)$field['value'], ENT_QUOTES, 'UTF-8')?>"
                                                >
                                            <?php } ?>
                                        </label>
                                    <?php } ?>
                                </div>
                            </section>
                        <?php } ?>
                    </div>

                    <div data-webshop-settings-feedback></div>

                    <button class="button button-primary" type="submit" use-waiting-icon="true">
                        <?=t('webshop_admin_settings_save', 'Instellingen opslaan')?>
                    </button>
                </form>
            </div>
        </div>
    </grid>
</section>
