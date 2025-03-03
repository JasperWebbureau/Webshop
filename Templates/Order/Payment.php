<?php

/**
 * @var $payment \App\Webshop\Payment\Payment;
 */
$form = new \Form\Form('payment');
$event = new \Event\AjaxEvent('App\Webshop\Payment\Payment', 'edit');
$form->addEvent($event);
$form->getFormElement()->setAttribute('ajax', 'true');
$form->getFormElement()->setAttribute('onfocusout', '$(this).trigger(\'submit\')');

foreach($payment->getPaymentMethods() as $paymentMethod){
    $radio = new \Form\Input\RadioInput('payment', 'payment');
    $radio->setValue($paymentMethod['name'])->setLabel($paymentMethod['name']);
    if($paymentMethod['name'] == $payment->getValue()){
        $radio->getInputElement()->setAttribute('checked', 'true');
    }
    $form->addInput($radio);
}
?>


<div class="card card--order">

    <div class="card__content>">
        <h3>Betaalwijze</h3>
        <?=$form?>

    </div>
</div>



