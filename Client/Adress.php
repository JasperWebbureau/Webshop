<?php
namespace App\Webshop\Client;

use App\Repository\WebshopClientAdressRepository;
use App\Repository\WebshopClientRepository;
use Factory\Form\FormFactory;
use Response\AjaxResponse;
use Utils\_Session;

class Adress{
    private $session = null;
    static $repository = null;
    static $user = null;
    public function __construct()
    {
        $this->session= new _Session('webshopAdress');
    }

    public function load()
    {
        if(self::$user != null){
            return self::$user;
        }
        if($this->session->getInt('id') > 0){
            self::$user = $this->getRepository()->findById($this->session->getInt('id'));
        }else{
            self::$user = $this->getRepository()->getNew();
            self::$user->populate($this->session->getArray());
        }
        return  self::$user;
    }

    public function edit()
    {
        $form = $this->getForm('clientAdress');
        $values = $form->getValuesFromRequest();
        $this->load();
        foreach($values as $key=>$value){
            $this->session->set($key, $value);
        }

        if(__AJAX__){
            $response = new AjaxResponse('');

            $response->setContainer('.card--ardress', (string)(new \App\Webshop\Controller\WebshopOrderController())->Adress());
            $response->setContainer('.card--order-button', (string)(new \App\Webshop\Controller\WebshopOrderController())->OrderButton());
            return $response;
        }

    }

    public function getForm($name = 'clientAdress')
    {
        $user = $this->load();

        $formFactory = new FormFactory($name);
        $formFactory->generateFromEntity($user);

        return $formFactory->getForm();
    }

    private function getRepository()
    {
        if(self::$repository != null){
            return self::$repository;
        }
        self::$repository = new \App\Webshop\Repository\WebshopClientAdressRepository();
        return self::$repository;
    }

    public function validate()
    {
        $this->load();

        //if any of the fields is empty;
        //
        $requiredFields = [
            'street'=>[ 'message'=>'Vul aub uw straat nog in'],
            'street_nr'=>[ 'message'=>'Vul aub uw huis nummer nog in'],
            'zip_code'=>[ 'message'=>'Vul aub uw postcode nog in'],
            'city'=>[ 'message'=>'Vul aub uw woonplaats nog in'],
        ];
        $emptyFields = [];

        foreach($requiredFields as $key=>$data){
            if($this->session->get($key) == ''){
                $emptyFields[$key] = $data['message'];
            }
        }

        return $emptyFields;

    }

    public function add($clientId)
    {
        $this->session->set('id', (int)$this->session->get('id'));
        $this->session->set('clientId', $clientId);
        $new = $this->getRepository()->getNew();
        $new->populate($this->session->getArray());

        $added =  $this->getRepository()->add($new);
        $this->session->set('id', $added->getId());
        return $added;

    }
}