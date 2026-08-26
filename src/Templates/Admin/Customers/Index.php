<?php
?>
<section class="webshop-admin-customers">
    <grid>
        <div style="--cw:12;--cw-sm:12;--cw-xs:12">
            <div class="webshop-admin-orders__header">
                <h1><?=t('webshop_admin_customers_overview_title', 'Klanten')?></h1>
                <a class="button button-outline" href="<?=__DOMAIN__?>/Flexgrid/WebshopAdmin/dashboard">
                    <i class="fas fa-arrow-left"></i> <?=t('webshop_admin_back_dashboard', 'Terug naar dashboard')?>
                </a>
            </div>
        </div>

        <div class="panel" style="--cw:12;--cw-sm:12;--cw-xs:12">
            <div class="panel__body">
                <form class="webshop-admin-filters" method="get" action="<?=__DOMAIN__?>/Flexgrid/WebshopAdmin/customers">
                    <input
                        type="search"
                        name="q"
                        value="<?=htmlspecialchars((string)($filters['q'] ?? ''), ENT_QUOTES, 'UTF-8')?>"
                        placeholder="<?=t('webshop_admin_customers_search_placeholder', 'Zoek op naam, bedrijf, e-mail of plaats')?>"
                    >
                    <button class="button button-primary button-small" type="submit">
                        <?=t('webshop_admin_filter_apply', 'Zoeken')?>
                    </button>
                    <?php if (trim((string)($filters['q'] ?? '')) !== '') { ?>
                        <a class="button button-outline button-small" href="<?=__DOMAIN__?>/Flexgrid/WebshopAdmin/customers">
                            <?=t('webshop_admin_filter_reset', 'Reset')?>
                        </a>
                    <?php } ?>
                </form>
                <div class="webshop-admin-orders__table">
                    <?php foreach ($customers as $customer) { ?>
                        <article class="webshop-admin-customers__row">
                            <div>
                                <strong><?=htmlspecialchars((string)$customer->getTitle(), ENT_QUOTES, 'UTF-8')?></strong>
                                <span><?=htmlspecialchars((string)$customer->getCompanyName(), ENT_QUOTES, 'UTF-8')?></span>
                            </div>
                            <div><?=htmlspecialchars((string)$customer->getEmail(), ENT_QUOTES, 'UTF-8')?></div>
                            <div><?=htmlspecialchars((string)$customer->getPhone(), ENT_QUOTES, 'UTF-8')?></div>
                            <div>
                                <?=htmlspecialchars((string)$customer->getPostalCode(), ENT_QUOTES, 'UTF-8')?>
                                <?=htmlspecialchars((string)$customer->getCity(), ENT_QUOTES, 'UTF-8')?>
                            </div>
                            <a class="button button-small" href="<?=__DOMAIN__?>/Flexgrid/WebshopAdmin/customerDetail/<?=(int)$customer->getId()?>">
                                <?=t('webshop_admin_customer_open', 'Open')?>
                            </a>
                        </article>
                    <?php } ?>
                    <?php if (empty($customers)) { ?>
                        <p><?=t('webshop_admin_customers_empty', 'Nog geen klanten.')?></p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </grid>
</section>
