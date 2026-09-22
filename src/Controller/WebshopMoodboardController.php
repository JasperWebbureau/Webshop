<?php

namespace Flexgrid\Modules\Webshop\Controller;

use Flexgrid\App\Settings\Settings;
use Flexgrid\Modules\Webshop\Repository\WebshopMoodboardRepository;
use Flexgrid\Response\TemplateResponse;

class WebshopMoodboardController
{
    /**
     * @FG\Template [name=Inspiratie blok, icon=fas fa-map-marker-alt, html={<div data-type='plugin'><h5>Inspiratie blok</h5></div>},create_override=true]
     * @param int $moodboardId [name=Moodboard,type=WebshopMoodboard]
     * @param int $productGridPageId [name=Product detail pagina,type=page]
     */
    public function moodboard($moodboardId = 0, $productGridPageId = 0)
    {
        $repository = new WebshopMoodboardRepository();
        $moodboard = (int)$moodboardId > 0 ? $repository->findById((int)$moodboardId) : ($repository->getActive(1)[0] ?? null);

        if (!$moodboard || (int)$moodboard->getId() <= 0) {
            return '';
        }


        if (!$isActive) {
          //  return '';
        }
        $file = 'Flexgrid/Modules/Webshop/src/Templates/Moodboard/Moodboard.php';
        if(file_exists('App/Webshop/Templates/Moodboard/Moodboard.php')){
            $file = 'App/Webshop/Templates/Moodboard/Moodboard.php';
        }
        return new TemplateResponse($file , [
            'moodboard' => $moodboard,
            'items' => $moodboard->getWebshopMoodboardItemChildren(),
            'productGridPageId' => (int)$productGridPageId ?: $this->getProductGridPageId(),
        ]);
    }

    protected function getProductGridPageId(): int
    {
        new Settings();
        $setting = Settings::get('page_product_grid_page_id', [
            'value' => 0,
            'label' => 'Product grid pagina',
        ]);

        return (int)($setting['value'] ?? 0);
    }
}
