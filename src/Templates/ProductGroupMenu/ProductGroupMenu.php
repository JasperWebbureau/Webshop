<?php

$limit = (int)($limit ?? 99);
$pageId = (int)($pageId ?? 0);
$items = array_slice($entities ?? [], 0, $limit > 0 ? $limit : 99);

foreach ($items as $productGroup) {
    $title = method_exists($productGroup, 'getTitle') ? (string)$productGroup->getTitle() : '';
    $href = method_exists($productGroup, 'getDetailUrl') ? (string)$productGroup->getDetailUrl($pageId) : '#';
    ?>
    <li>
        <a href="<?=htmlspecialchars($href, ENT_QUOTES, 'UTF-8')?>" aria-label="Productgroep: <?=htmlspecialchars($title, ENT_QUOTES, 'UTF-8')?>">
            <?=htmlspecialchars($title, ENT_QUOTES, 'UTF-8')?>
        </a>
    </li>
<?php }
