<?php if ($pageId > 0 && !empty($groups)) { ?>
<nav class="vertical" aria-label="<?=htmlspecialchars(t('webshop_footer_main_groups_label', 'Webshop hoofdgroepen'), ENT_QUOTES, 'UTF-8')?>">
    <ul>
        <?php foreach ($groups as $group) { ?>
            <li><a href="<?=htmlspecialchars($group->getDetailUrl($pageId), ENT_QUOTES, 'UTF-8')?>"><?=htmlspecialchars((string)$group->getTitle(), ENT_QUOTES, 'UTF-8')?></a></li>
        <?php } ?>
    </ul>
</nav>
<?php } ?>
