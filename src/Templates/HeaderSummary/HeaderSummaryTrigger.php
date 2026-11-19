<?php
// Shared action markup; the two counters keep their public Ajax selectors.
$isSearch = !empty($searchId);
$triggerClass = 'webshop-header-summary__trigger' . ($isSearch ? ' webshop-search__trigger' : '');
?>
<?php if ($isSearch) { ?>
<button type="button" class="<?=$triggerClass?>" aria-label="<?=htmlspecialchars((string)($label), ENT_QUOTES, 'UTF-8')?>" aria-expanded="false" aria-controls="<?=htmlspecialchars((string)($searchId), ENT_QUOTES, 'UTF-8')?>-popover" data-webshop-search-trigger>
<?php } else { ?>
<a class="<?=$triggerClass?>" href="<?=htmlspecialchars((string)($url !== '' ? $url : '#'), ENT_QUOTES, 'UTF-8')?>" aria-label="<?=htmlspecialchars((string)($label), ENT_QUOTES, 'UTF-8')?>">
<?php } ?>
    <span class="webshop-header-summary__icon"><i class="<?=htmlspecialchars((string)($icon), ENT_QUOTES, 'UTF-8')?>" aria-hidden="true"></i></span>
    <?php if (!$isSearch) { ?>
        <span class="webshop-header-summary__count <?=htmlspecialchars((string)($countClass), ENT_QUOTES, 'UTF-8')?>"><?=(int)$quantity?></span>
    <?php } ?>
<?php if ($isSearch) { ?></button><?php } else { ?></a><?php } ?>
