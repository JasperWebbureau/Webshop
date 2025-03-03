<?php

/**
 * @var $Delivery \App\Webshop\Delivery\Delivery;
 */
$form = new \Form\Form('delivery');
$event = new \Event\AjaxEvent('App\Webshop\Delivery\Delivery', 'edit');
$form->addEvent($event);
$form->getFormElement()->setAttribute('ajax', 'true');
$form->getFormElement()->setAttribute('onfocusout', '$(this).trigger(\'submit\')');
$currentMethod = $Delivery->getValue();
foreach($Delivery->getDeliveryMethods() as $deliveryMethod) {
    $radio = new \Form\Input\RadioInput('delivery', 'delivery');
    $title =$deliveryMethod->getTitle();
    if($deliveryMethod->getPrice() > 0){
        $price = new \Utils\_Price($deliveryMethod->getPrice());
        $title .= ' (' . $price->getHtml() . ')';
    }
    if($currentMethod == 0){

    }

    $radio->setValue($deliveryMethod->getId())->setLabel($title);
    if($deliveryMethod->getId() == $Delivery->getValue()){
        $radio->getInputElement()->setAttribute('checked', 'true');
    }
    $form->addInput($radio);
}
?>


<?php if(! __AJAX__){ ?>
<div class="card card--order card--shipping">
    <?php } ?>
    <div class="card__content>">
        <h3>Verzend wijze</h3>
        <?=$form?>
    </div>
<?php if(! __AJAX__){ ?>
    </div>
<?php } ?>

