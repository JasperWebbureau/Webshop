<?php
// Use the same owning slider component as ModuleController::slider().
echo new \Flexgrid\Response\TemplateResponse('App/Slider/Slider.php', [
    'entities' => $entities, 'card' => $card, 'pageId' => $pageId,
    'cardWidthDesktop' => $cardWidthDesktop, 'cardWidthTablet' => $cardWidthTablet,
    'pageButton' => $pageButton, 'parentWidth' => $parentWidth,
    'useControls' => $useControls, 'autoPlay' => $autoPlay,
]);
