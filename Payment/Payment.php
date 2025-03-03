<?php
namespace App\Webshop\Payment;
use App\Webshop\Entity\WebshopDeliveryMethod;
use App\Webshop\Repository\WebshopDeliveryMethodRepository;
use Utils\_Session;

class Payment{
    private $session = null;

    public function __construct()
    {
        $this->session= new _Session('webshopPayment');
    }
    public function edit()
    {
        $this->session->set('payment', $_REQUEST['payment']['payment']);
        //dump($this->session->getArray());

    }


    public function getPaymentMethods()
    {
        return [['name'=>'Ideal'],['name'=>'Credit card']];
    }

    public function getValue()
    {
        return $this->session->get('payment');
    }

    public function validate()
    {
        //if any of the fields is empty;
        //
        $requiredFields = ['payment'];
        $emptyFields = [];

        foreach($requiredFields as $key){
            if($this->session->get($key) == ''){
                $emptyFields[$key] = $key;
            }
        }

        return $emptyFields;

    }

    public function getAll(){
        return (new WebshopDeliveryMethodRepository())->getAll();
    }
}