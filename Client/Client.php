<?php
namespace App\Webshop\Client;

use App\Repository\WebshopClientRepository;
use Factory\Form\FormFactory;
use Response\AjaxResponse;
use Utils\_Session;

class Client{
    private $session = null;
    static $repository = null;
    static $user = null;
    public function __construct()
    {
        $this->session= new _Session('webshopClient');

    }

    public function load()
    {
        if(self::$user != null){
            return self::$user;
        }
        if($this->session->getInt('id') > 0){
            // load from db
            self::$user = $this->getRepository()->findById($this->session->getInt('id'));
        }else{
            self::$user = $this->getRepository()->getNew();
            self::$user->populate($this->session->getArray());
        }

        return  self::$user;
    }

    public function edit()
    {
        $form = $this->getForm('clientOrder');
        $values = $form->getValuesFromRequest();

        $this->load();
        foreach($values as $key=>$value){

            $this->session->set($key, $value);
        }



        if(__AJAX__){
            $response = new AjaxResponse('');

            $response->setContainer('.card--client', (string)(new \App\Webshop\Controller\WebshopOrderController())->User());

            $response->setContainer('.card--order-button', (string)(new \App\Webshop\Controller\WebshopOrderController())->OrderButton());

            return $response;
        }

    }


    public function getForm($name = 'client')
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
        self::$repository = new WebshopClientRepository();
        return self::$repository;
    }

    public function validate()
    {
        $this->load();

        //if any of the fields is empty;
        //
        $requiredFields = [
            'name'=>[ 'message'=>'Vul aub uw Naam nog in'],
            'surname'=>[ 'message'=>'Vul aub uw Achternaam nog in'],
            'email'=>[ 'message'=>'Vul aub uw Email adress nog in'],
            'phone'=>[ 'message'=>'Vul aub uw Telefoon nummer nog in'],];
        $emptyFields = [];
        foreach($requiredFields as $key=>$data){
            if($this->session->get($key) == ''){
                $emptyFields[$key] = $data['message'];
            }
        }
        $email = $this->session->get('email');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) && $email != ''){
            $emptyFields['email_incorrect'] =  'Vul aub een geldig email adress nog in';
        }
        return $emptyFields;

    }

    public function add()
    {
        $this->session->set('id', (int)$this->session->get('id'));
        $new = $this->getRepository()->getNew();
        $new->populate($this->session->getArray());
        $added =  $this->getRepository()->add($new);
        $this->session->set('id', $added->getId());
        return $added;

    }
}