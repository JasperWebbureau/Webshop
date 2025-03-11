<?php
namespace App\Webshop\Controller;

use App\Service\Entity\Service;
use App\Service\Entity\ServiceGroup;
use App\Service\Repository\ServiceGroupRepository;
use App\Service\Repository\ServiceRepository;
use Controller\Controller;
use Controller\ModuleController;
use Flexgrid\Entity\Url;
use Flexgrid\FlexGrid;
use Response\Metadata\Metadata;
use Response\PageResponse;
use Response\TemplateResponse;

/**
 * Class ServiceController
 * @package App\Controller
 * @FG\Controller [name=Cart, type=Webshop, Icon=fas fa-plugin]
 */

class WebshopCartController extends Controller
{
    /**
     * @FG\Template [name=Cart , icon=fas fa-shopping-cart];
     */
    public function Cart()
    {
        $response =  new TemplateResponse('App/Webshop/Templates/ShoppingCart/cartButton.php' ,[]);

        return $response;
    }

    /**
     * @FG\Template [name=Winkelwagen overzicht , icon=fas fa-shopping-cart];
     */
    public function CartOverview()
    {
        $response =  new TemplateResponse('App/Webshop/Templates/ShoppingCart/cartOverview.php' ,['editable'=>true]);

        return $response;
    }
}