<?php
namespace App\Webshop\Controller;

use App\Service\Entity\Service;
use App\Service\Entity\ServiceGroup;
use App\Service\Repository\ServiceGroupRepository;
use App\Service\Repository\ServiceRepository;
use App\Webshop\Repository\WebshopProductRepository;
use Controller\Controller;
use Controller\ModuleController;
use Flexgrid\Entity\Url;
use Flexgrid\FlexGrid;
use Response\AjaxResponse;
use Response\Metadata\Metadata;
use Response\PageResponse;
use Response\TemplateResponse;

/**
 * Class ServiceController
 * @package App\Controller
 * @FG\Controller [name=Product, type=Webshop, Icon=fas fa-plugin]
 */

class WebshopProductController extends ModuleController
{
    /**
     * @FG\Template [name=Product Detail Titel , icon=fas fa-bars];
     */
    public function productTitle()
    {


        $entity = FlexGrid::getApp()->server->getPath()->getEntity();
        return new TemplateResponse('Templates\Webshop\ProductDetail\title.php', ['product'=>$entity ]);

    }

    /**
     * @FG\Template [name=Product Detail Omschrijving , icon=fas fa-font];
     */
    public function productDescription()
    {
        $entity = FlexGrid::getApp()->server->getPath()->getEntity();
        return new TemplateResponse('App/Webshop/Templates/ProductDetail/description.php', ['product'=>$entity ]);

    }

    /**
     * @FG\Template [name=Product Detail Afbeeldingen , icon=fas fa-bars];
     */
    public function productImages()
    {


        $entity = FlexGrid::getApp()->server->getPath()->getEntity();
        return new TemplateResponse('App/Webshop/Templates/ProductDetail/images.php', ['product'=>$entity ]);

    }

    /**
     * @FG\Template [name=Product Intro  , icon=fas fa-bars];
     */
    public function productIntro()
    {


        $entity = FlexGrid::getApp()->server->getPath()->getEntity();
        return new TemplateResponse('Templates\Webshop\ProductDetail\intro.php', ['product'=>$entity ]);

    }

    /**
     * @FG\Template [name=Product Prijs  , icon=fas fa-bars];
     */
    public function productPrice()
    {


        $entity = FlexGrid::getApp()->server->getPath()->getEntity();
        return new TemplateResponse('Templates\Webshop\ProductDetail\price.php', ['product'=>$entity ]);

    }

    /**
     * @FG\Template [name=Voeg Toe  , icon=fas fa-bars];
     */
    public function productAdd()
    {

        $entity = FlexGrid::getApp()->server->getPath()->getEntity();
        return new TemplateResponse('App/Webshop/Templates/ProductDetail/add.php', ['product'=>$entity ]);

    }

    /**
     * @FG\Template [name=Specificaties  , icon=fas fa-bars];
     */
    public function specifications()
    {


        $entity = FlexGrid::getApp()->server->getPath()->getEntity();
        return new TemplateResponse('Templates\Webshop\ProductDetail\specifications.php', ['product'=>$entity ]);

    }


    /**
     * @FG\Template [name=Product Overzicht , icon=fas fa-bars];
     */
    public function product( $limit = '999', $width = '')
    {

        $showMainArticlesFirst = 0;
        // PageResponse::addAsset('Templates/Modules/Product/Card/Css/'.$card. '.scss');
        $productRepo =  (new WebshopProductRepository());
        $entity = FlexGrid::getApp()->server->getPath()->getEntity();

        $filters = $this->getFilters();

        $select =$productRepo->select(true);
        if($filters['query']!= null){
            $select->where($filters['query']);
        }

        if($entity != null){
            if($showMainArticlesFirst == 1){
                $select->where('(main_article = 0 OR main_article IS NULL )');
            }

            $select->where('group_id = ?',[$entity->getId()]);
            if($limit != ''){
                $select->limit($limit);
            }
            $products = $select->get();
        }else{
            $products = $productRepo->getAll($limit);
        }

        $response = new TemplateResponse('App/Webshop/Templates/Product/grid.php', ['products'=>$products, 'width'=>$width ]);;
        if($this->request->get('ajax') == 'true' ){
            $ajax = new AjaxResponse();
            $ajax->setContainer('[data-filter-result="'. md5(get_class($this)). '"]',(string)$response);
            return $ajax;
        }
        // dump($products);
        return $response;

    }

}