<?php
namespace App\Webshop\Cart;

//Always one cart;

use Cache\CacheFile;
use Flexgrid\App\Settings\Settings;
use Request\Request;
use Response\AjaxResponse;
use Response\TemplateResponse;
use ScssPhp\ScssPhp\Cache;
use Utils\_Session;

Class Cart{
    static $instance = null;
    public $data;
    private $orderPage = null;
    public function __construct()
    {

        $this->data = new _Session('cart');
    }
    public static function getInstance()
    {

        if (!isset(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function removeProduct($id = null)
    {
        $rq = new Request();
        if($id == null){

            $id = $rq->getInt('id');


            $products = $this->data->get('products') ?: [];
            unset($products[$id]);
            $this->data->set('products', $products);
        }
    }
    public function addArticle($id = null, $amount = null)
    {
        $rq = new Request('cart');
        if($id == null &&  $amount == null){

            $id = $rq->getInt('id');
            $amount = $rq->getInt('amount');


        }
        $products = $this->data->get('products') ?: [];
        if(! empty($products[$id])){
            $products[$id]['amount'] += $amount;
        }else{
            $products[$id]['amount'] = $amount;
        }
        $products[$id]['amount'] = 1;
        $this->data->set('products', $products);


        $response = new AjaxResponse('');
        $response->setContainer('.shopping-cart-small-wrapper', (string)new TemplateResponse('Templates/Webshop/ShoppingCart/cartButton.php', ['force'=>true]));
        return $response;
    }


    public function getProductAmount()
    {
        $amount = 0;
       // dump($this->data);
        foreach($this->data->get('products') as $key=>$row){

            $amount += $row['amount'];
        }

        return $amount;
    }
    public function getProducts()
    {
         return $this->data->get('products') ?? [];
    }


    public function getAddForm($product, $amount = 1, $submit = true){
        /**
         * @var  $product \App\Entity\Product;
         */
        $formname = 'cart';
        $form = new \Form\Form($formname);
        $idInput = new \Form\Input\HiddenInput('id',$formname);
        $ammountInput = new \Form\Input\NumberInput('amount', $formname);
        $ammountInput->setValue($amount);
        $idInput->setValue($product->getId());
        $form->addInput($idInput);
        $form->addInput($ammountInput);
        $form->setMethod('ajax');

        $form->addEvent((new \Event\AjaxEvent('App\Webshop\Cart\Cart', 'addArticle' )));
        if($submit) {
            $form->addInput('<button type="submit" class="button button-primary">Voeg toe <i class="fas fa-plus"></i></button>');
        }
        return $form;
    }

    public function getMiniCartHtml(){
        $file = new CacheFile('minicart.txt');
        return $file->getContent();
    }
    public function setMiniCartHtml($content){
        $file = new CacheFile('minicart.txt');
        $file->setContent($content);
    }

    public function getOrderPage()
    {
        new Settings();
        if($this->orderPage == null){
            $pageRepository = new \Flexgrid\Repository\PageRepository();
            $this->orderPage = $pageRepository->findById(\ Flexgrid\App\Settings\Settings::Page('shopping-order'));
        }

        return $this->orderPage;
    }
}