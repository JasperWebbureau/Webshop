<?php
/**
 * @var $entity \Flexgrid\Modules\Webshop\Entity\WebshopProductMainGroup|null
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
</section>
