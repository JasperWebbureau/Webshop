<?php
/**
 * @var $entity \Flexgrid\Modules\Webshop\Entity\WebshopProductMainGroup|null
 */
if (!$entity) {
    return;
}

$text = trim((string)$entity->getHighlightText());
$image = $entity->getHighlightImage();
?>
<?php if ($text !== '') { ?>
    <section class="webshop-main-group-highlight">
        <div class="webshop-main-group-highlight__image">
            <img src="<?=$image->getResizeUrl(980, 760, 1)?>" alt="<?=htmlspecialchars((string)$entity->getTitle(), ENT_QUOTES, 'UTF-8')?>" loading="lazy">
        </div>
        <div class="webshop-main-group-highlight__content">
            <?=$text?>
            <?php if (trim((string)$buttonText) !== '' && trim((string)$buttonUrl) !== '') { ?>
                <a href="<?=htmlspecialchars((string)$buttonUrl, ENT_QUOTES, 'UTF-8')?>"><?=htmlspecialchars((string)$buttonText, ENT_QUOTES, 'UTF-8')?> <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
            <?php } ?>
        </div>
    </section>
<?php } ?>
