<?php
namespace App\Webshop\Controller;

use App\Service\Entity\Service;
use App\Service\Entity\ServiceGroup;
use App\Service\Repository\ServiceGroupRepository;
use App\Service\Repository\ServiceRepository;
use App\Webshop\Cart\Cart;
use App\Webshop\Client\Adress;
use App\Webshop\Client\Client;
use App\Webshop\Delivery\Delivery;
use App\Webshop\Payment\Payment;
use App\Webshop\Webshop\Orders;
use App\Webshop\Webshop\Webshop;
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
    public function __construct()
    {
        PageResponse::addAsset('App/Webshop/Templates/Order/Css/Order.scss');



    }
    /**
     * @FG\Template [name=Logica block voor bestelling plaatsen , icon=fas fa-heading];
     */
    public function PlaceOrder()
    {
        $webshop = new Webshop();
        if( ! $webshop->placeOrder()){
            //redirect to order page;
            $cart =new Cart();
            FlexGrid::redirect($cart->getOrderPage()->getUrl());
            return false;
        };;

        $response =  new TemplateResponse('' ,[]);

        return $response;
    }

    /**
     * @FG\Template [name=Order , icon=fas fa-heading];
     */
    public function Order()
    {

        $response =  new TemplateResponse('' ,[
            'entityCollection'=>$this->getRepository()->getAll($limit), 'card'=>$card]);

        return $response;
    }

    /**
     * @FG\Template [name=Bestel overzicht Gebruiker , icon=fas fa-user];
     */
    public function User()
    {
        $client = new Client();
        $template = 'App/Webshop/Templates/Order/User.php';
        $response =  new TemplateResponse($template ,['client'=>$client]);

        return $response;
    }

    /**
     * @FG\Template [name=Bestel overzicht Adress , icon=fas fa-home];
     */
    public function Adress()
    {
        $address = new Adress();
        $template = 'App/Webshop/Templates/Order/Adress.php';
        $response =  new TemplateResponse($template ,['address'=>$address]);

        return $response;
    }

    /**
     * @FG\Template [name=Bestel overzicht Winkelwagen , icon=fas fa-shopping-cart];
     */
    public function ShoppingCart()
    {

        $template = 'App/Webshop/Templates/ShoppingCart/cartOverview.php';
        $response =  new TemplateResponse($template ,[]);

        return $response;
    }
    /**
     * @FG\Template [name=Bestel overzicht Betaalwijze , icon=fas fa-shopping-cart];
     */
    public function PaymentMethod()
    {
        $template = 'App/Webshop/Templates/Order/Payment.php';
        $payments = new Payment();

        $response =  new TemplateResponse($template ,['payment'=>$payments, 'methods'=>$payments->getAll()]);

        return $response;
    }
    /**
     * @FG\Template [name=Bestel overzicht Verzendwijze , icon=fas fa-truck];
     */
    public function DeliveryMethod()
    {
        $template = 'App/Webshop/Templates/Order/Delivery.php';
        $Delivery = new Delivery();
        $response =  new TemplateResponse($template ,['Delivery'=>$Delivery]);

        return $response;
    }
    /**
     * @FG\Template [name=Bestel knop , icon=fas fa-dollar-sign];
     */
    public function OrderButton()
    {
        $template = 'App/Webshop/Templates/Order/Button.php';
        $order = new Orders();
        $response =  new TemplateResponse($template ,['orders'=>$order]);

        return $response;
    }


}