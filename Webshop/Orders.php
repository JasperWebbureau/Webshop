<?php
namespace App\Webshop\Webshop;
use App\Webshop\Client\Adress;
use App\Webshop\Client\Client;
use App\Webshop\Delivery\Delivery;
use App\Webshop\Entity\WebshopClient;
use App\Webshop\Entity\WebshopClientAdress;
use App\Webshop\Entity\WebshopDeliveryMethod;
use App\Webshop\Entity\WebshopOrder;
use App\Webshop\Entity\WebshopOrderRow;
use App\Webshop\Entity\WebshopProduct;
use App\Webshop\Payment\Payment;
use App\Webshop\Repository\WebshopOrderRepository;
use App\Webshop\Repository\WebshopOrderRowRepository;
use App\Webshop\Repository\WebshopProductRepository;
use Flexgrid\Controllers\Settings;
use Flexgrid\FlexGrid;
use Utils\_Mail;
use Utils\_Price;
use Utils\_Session;
use Utils\Payments\Mollie\Mollie;

class Orders{
    static $validated = null;
    public function __construct()
    {
        $this->session= new _Session('webshopOrder');
    }



    public function placeOrder()
    {

        $orderRepository = new WebshopOrderRepository();

        if($_REQUEST['id'] > 0){
            // ok so some external entity is asking to get the order (probably webhook)
            $order = $orderRepository->findById((int)$_REQUEST['id']);
        }else{
            // the order hasn't been placed yet.
            $result = $this->validateOrderData();
            if($result['result'] == false){

                return false;
            }
            // ok everything is set:)
            $order = $this->addOrder($result);
        }
        /**
         * @var $order WebshopOrder;
         */
        $mollie = new Mollie();
        $payment = $mollie->findPayment($order->getPaymentId());
        if($payment->status == 'paid'){
            $order->setIsPaid(1);
            $orderRepository->add($order);
            //clear order id
            $this->session->set('id', 0);
        }

        if($order->getIsPaid() == 0){
            header('Location: '. $order->getPaymentLink());
            die();
        }

        if($order->getIsPaid() == 1){
            // redirect to thank you page
            new \Flexgrid\App\Settings\Settings();
            header('Location: http://localhost/huisje102025//bestellen/bestelling-plaatsen/bedankt');
        }

        return false;


    }


    private function addOrder($result)
    {
        $client = $result['client']['class']->add();
        $address = $result['address']['class']->add($client->getId());
        $delivery = $result['delivery']['class'];

        $order = new WebshopOrder();
        $order->setClientId($client->getId());
        $order->setAddressId($address->getId());
        $orderRepository = new WebshopOrderRepository();
        $addedOrder =  $orderRepository->add($order);
        $this->session->set('id', $addedOrder->getId());
        $orderId = $addedOrder->getId();
        $cart = \App\Webshop\Cart\Cart::getInstance();
        $productRepo  = new WebshopProductRepository();
        $orderRowRepo = new WebshopOrderRowRepository();
        $price = new _Price(0);
        foreach($cart->getProducts() as $id=> $product){
            /**
             * @var $new WebshopOrderRow;
             * @var $productObject  WebshopProduct;
             */
            $productObject = $productRepo->findById($id);

            if($productObject->getId() == 0){
                continue;
            }
            $new = $orderRowRepo->getNew();

            $new->setAmount($product['amount']);
            $new->setPrice($productObject->getPrice());
            $new->setVat($productObject->getVat());
            $new->setTitle($productObject->getTitle());
            $new->setOrderId($orderId);

            $price->add($product['amount'] * $productObject->getPrice());

            $orderRowRepo->add($new);
        }
        //clear cart here

        //check for delivery costs
        $deliveryMethod = $delivery->getSelected();
        /**
         * @var WebshopDeliveryMethod $deliveryMethod;
         */

        if($deliveryMethod->getPrice() > 0){

            $price->add($deliveryMethod->getPrice());
        };

        $mollie = new Mollie();
        $mollie
            ->setPrice($price)
            ->setDescription('Bestelling met id: ' .$addedOrder->getId() . ' Van webwinkel '.\Flexgrid\App\Settings\Settings::Company('name')  )
            ->setRedirectUrl('https://webbureau.nu')
            ->setWebhookUrl('https://webbureau.nu');

        $payment = $mollie->getPayment();

        $addedOrder->setPaymentLink($payment->getCheckoutUrl());
        $addedOrder->setPaymentId($payment->id);
        $orderRepository->add($addedOrder);
        $this->notifyOwnerAboutNewOrder($addedOrder);
        return $addedOrder;


    }
    public function validateOrderData()
    {
        //now check if we have all the data;
        $validated = ['result'=>true];
        $client = new Client();
        $clientArray = $client->validate();
        $validated['client']['errors'] = $clientArray;
     $validated['client']['class'] = $client;
        if(!empty($clientArray)){
            $validated['result'] = false;
        }


        $address = new Adress();
        $addressArray = $address->validate();
     $validated['address']['errors'] = $addressArray;
     $validated['address']['class'] = $address;
        if(!empty($addressArray)){
         $validated['result'] = false;
        }

        $payment = new Payment();
        $paymentArray = $payment->validate();
     $validated['payment']['errors'] = $paymentArray;
     $validated['payment']['class'] = $payment;
        if(!empty($paymentArray)){
         $validated['result'] = false;
        }

        $delivery = new Delivery();
        $deliveryArray =  $delivery->validate();
     $validated['delivery']['errors'] = $deliveryArray;
     $validated['delivery']['class'] = $delivery;
        if(!empty($deliveryArray)){
         $validated['result'] = false;
        }

        return $validated ;


    }

    private function checkFields()
    {

    }

    static function getErrorMessage($key,$value)
    {
        return $value;

        return  $defaults[$key] ?? $key;
    }

    public function getPlaceOrderPage()
    {
        if($this->placeOrderPage == null){
            new \Flexgrid\App\Settings\Settings();
            $pageRepository = new \Flexgrid\Repository\PageRepository();
            $this->placeOrderPage = $pageRepository->findById(\ Flexgrid\App\Settings\Settings::Page('place-order'));
        }

        return $this->placeOrderPage;
    }

    public function notifyOwnerAboutOrderPayment($order)
    {
        $mail = new _Mail();
        $mail

            ->setReciever()->setSender()->setHtml('De bestelling met ID:'. $order->getId(). 'Is betaald')
            ->setSubject('Bestelling betaald');

        $mail->send();


    }

    public function notifyOwnerAboutNewOrder($order)
    {
        $mail = new _Mail();
        $mail

            ->setReciever()->setSender()->setHtml('Er is een nieuwe bestelling geplaatst met ID:'. $order->getId(). 'Is betaald')
            ->setSubject('Bestelling Geplaatst');

        $mail->send();


    }

}