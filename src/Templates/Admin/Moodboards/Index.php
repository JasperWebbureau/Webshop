<?php
/**
 * @var $moodboards \Flexgrid\Modules\Webshop\Entity\WebshopMoodboard[]
 * @var $query string
 */
?>
<section class="webshop-admin-moodboards">
    <grid>
        <div style="--cw:12;--cw-sm:12;--cw-xs:12">
            <div class="webshop-admin-moodboards__header">
                <h1><?=t('webshop_admin_moodboards_overview_title', 'Inspiratieblokken')?></h1>
                <div class="webshop-admin-moodboards__actions">
                    <a class="button button-primary" href="<?=__DOMAIN__?>/Flexgrid/WebshopAdmin/moodboardDetail/0">
                        <i class="fas fa-plus"></i> <?=t('webshop_admin_moodboard_new', 'Nieuw inspiratieblok')?>
                    </a>
                    <a class="button button-outline" href="<?=__DOMAIN__?>/Flexgrid/WebshopAdmin/dashboard">
                        <i class="fas fa-arrow-left"></i> <?=t('webshop_admin_back_dashboard', 'Terug naar dashboard')?>
                    </a>
                </div>
            </div>
        </div>

        <div class="panel" style="--cw:12;--cw-sm:12;--cw-xs:12">
            <div class="panel__body">
                <form class="webshop-admin-moodboards__filters" method="get" action="<?=__DOMAIN__?>/Flexgrid/WebshopAdmin/moodboards">
                    <input
                        type="search"
                        name="q"
                        value="<?=htmlspecialchars((string)$query, ENT_QUOTES, 'UTF-8')?>"
                        placeholder="<?=t('webshop_admin_moodboards_search_placeholder', 'Zoek op titel of label')?>">
                    <button class="button button-primary button-small" type="submit">
                        <?=t('webshop_admin_filter_apply', 'Filteren')?>
                    </button>
                    <?php if (trim((string)$query) !== '') { ?>
                        <a class="button button-outline button-small" href="<?=__DOMAIN__?>/Flexgrid/WebshopAdmin/moodboards">
                            <?=t('webshop_admin_filter_reset', 'Reset')?>
                        </a>
                    <?php } ?>
                </form>

                <div class="webshop-admin-moodboards__table">
                    <?php foreach ($moodboards as $moodboard) {
                        $isActive = (string)$moodboard->getIsActive() !== '0';
                        ?>
                        <article class="webshop-admin-moodboards__row">
                            <div class="webshop-admin-moodboards__thumb">
                                <img src="<?=$moodboard->getImage()->getResizeUrl(180, 120, 1)?>" alt="">
                            </div>
                            <div>
                                <strong><?=htmlspecialchars((string)$moodboard->getTitle(), ENT_QUOTES, 'UTF-8')?></strong>
                                <?php if (trim((string)$moodboard->getEyebrow()) !== '') { ?>
                                    <span><?=htmlspecialchars((string)$moodboard->getEyebrow(), ENT_QUOTES, 'UTF-8')?></span>
                                <?php } ?>
                                <span><?=count($moodboard->getWebshopMoodboardItemChildren())?> <?=t('webshop_admin_moodboard_items', 'producten')?></span>
                            </div>
                            <div>
                                <span class="webshop-admin-moodboards__status <?=$isActive ? 'is-active' : 'is-inactive'?>">
                                    <?=$isActive ? t('webshop_admin_active_yes', 'Actief') : t('webshop_admin_active_no', 'Niet actief')?>
                                </span>
                            </div>
                            <a class="button button-small" href="<?=__DOMAIN__?>/Flexgrid/WebshopAdmin/moodboardDetail/<?=(int)$moodboard->getId()?>">
                                <?=t('webshop_admin_moodboard_open', 'Open')?>
                            </a>
                        </article>
                    <?php } ?>
                    <?php if (empty($moodboards)) { ?>
                        <p><?=t('webshop_admin_moodboards_empty', 'Geen inspiratieblokken gevonden.')?></p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </grid>
</section>
