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
 * @FG\Controller [name=Order, type=Webshop, Icon=fas fa-plugin]
 */

class WebshopOrderController extends Controller
{
    /**
     * @FG\Template [name=Order , icon=fas fa-heading];
     */
    public function Order()
    {

        $response =  new TemplateResponse('' ,[
            'entityCollection'=>$this->getRepository()->getAll($limit), 'card'=>$card]);

        return $response;
    }

}