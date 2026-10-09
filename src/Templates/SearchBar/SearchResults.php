<?php

/**
 * @var string $cardFile
 * @var int $pageId
 */

use Flexgrid\Response\TemplateResponse;

$items = is_array($entities ?? null) ? $entities : [];
?>
<?php if (!$items) { ?>
    <p class="webshop-search__message"><?=htmlspecialchars(t('webshop_search_empty', 'Geen producten gevonden.'), ENT_QUOTES, 'UTF-8')?></p>
<?php } else { ?>
    <div class="webshop-search__items">
        <?php foreach ($items as $product) { ?>
            <?=new TemplateResponse($cardFile, [
                'entity' => $product,
                'pageId' => (int)$pageId,
                'cardWidth' => 12,
            ])?>
        <?php } ?>
    </div>
<?php } ?>
