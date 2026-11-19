<?php
use Flexgrid\Response\TemplateResponse;
echo new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/ProductCard/ProductCardContent.php', [
    'entity' => $entity, 'pageId' => $pageId ?? 0, 'cardWidth' => $cardWidth ?? 4,
    'cardModifier' => 'visual', 'showDescription' => false,
]);
