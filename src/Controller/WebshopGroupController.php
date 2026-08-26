<?php

namespace Flexgrid\Modules\Webshop\Controller;

use Flexgrid\Controller\ModuleController;

class WebshopGroupController extends ModuleController
{
    public function productGroupGrid($pageId = 0, $mainGroupId = 0, $limit = 99, $cardWidth = 4, $card = 'ProductGroupCard')
    {
        return (new WebshopProductGroupController())->productGroupGrid($pageId, $mainGroupId, $limit, $cardWidth, $card);
    }

    public function productGroupMenu($limit = 99, $pageId = 0, $mainGroupId = 0)
    {
        return (new WebshopProductGroupController())->productGroupMenu($limit, $pageId, $mainGroupId);
    }

    public function productMainGroupMenu($limit = 99, $pageId = 0, $groupLimit = 99)
    {
        return (new WebshopProductMainGroupController())->productMainGroupMenu($limit, $pageId, $groupLimit);
    }
}
