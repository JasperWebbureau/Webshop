<?php
/**
 * @var $entity \Flexgrid\Modules\Webshop\Entity\WebshopProductMainGroup|null
 */
if (!$entity) {
    return;
}

$title = trim((string)$entity->getCtaTitle());
$description = trim((string)$entity->getCtaDescription());
?>

<?php if ($title !== '' || $description !== '') { ?>
    <section class="webshop-main-group-cta">
        <grid>
            <div class="webshop-main-group-content">
                <?php if ($title !== '') { ?>
                    <h2><?=htmlspecialchars($title, ENT_QUOTES, 'UTF-8')?></h2>
                <?php } ?>
                <div class="webshop-main-group-cta__body">
                    <?php if ($description !== '') { ?>
                        <p><?=nl2br(htmlspecialchars($description, ENT_QUOTES, 'UTF-8'))?></p>
                    <?php } ?>
                    <?php if (trim((string)$buttonText) !== '' && trim((string)$buttonUrl) !== '') { ?>
                        <a href="<?=htmlspecialchars((string)$buttonUrl, ENT_QUOTES, 'UTF-8')?>"><?=htmlspecialchars((string)$buttonText, ENT_QUOTES, 'UTF-8')?> <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                    <?php } ?>
                </div>
            </div>

        </grid>
    </section>
<?php } ?>
