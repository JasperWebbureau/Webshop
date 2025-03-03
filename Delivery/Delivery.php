<?php
namespace App\Webshop\Delivery;

use App\Webshop\Repository\WebshopDeliveryMethodRepository;
use Response\AjaxResponse;
use Utils\_Session;

class Delivery{
    private $session = null;
    static $methods = null;
    public function __construct()
    {
        $this->session= new _Session('webshopDelivery');
    }


    public function getDeliveryMethods()
    {
        return $this->getAll();
        return [['name'=>'Verzenden'],['name'=>'Ophalen']];
    }

    public function edit()
    {
        $this->session->set('delivery', $_REQUEST['delivery']['delivery']);
        //dump($this->session->getArray());

        if(__AJAX__){
            $response = new AjaxResponse('');

            $response->setContainer('.card--shipping', (string)(new \App\Webshop\Controller\WebshopOrderController())->DeliveryMethod());
            $response->setContainer('.card--order-button', (string)(new \App\Webshop\Controller\WebshopOrderController())->OrderButton());
            return $response;
        }
    }

    public function getForm()
    {

    }

    public function validate()
    {
        //if any of the fields is empty;
        //
        $requiredFields = ['delivery'];
        $emptyFields = [];

        foreach($requiredFields as $key){
            if($this->session->get($key) == ''){
                $emptyFields[$key] = $key;
            }
        }

        return $emptyFields;

    }

    public function getValue()
    {
        return $this->session->get('delivery');
    }


    public function getAll(){
        if(self::$methods == null){
            self::$methods = (new WebshopDeliveryMethodRepository())->getAll();
        }
        return self::$methods;
    }
}