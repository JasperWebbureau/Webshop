<?php
/**
 * @var $client \App\Webshop\Client\Client;
 */

    $form = $address->getForm('clientAdress');

    $event = new \Event\AjaxEvent('App\Webshop\Client\Adress', 'edit');
    $form->addEvent($event);
    $form->getFormElement()->setAttribute('ajax', 'true');
    $form->getFormElement()->setAttribute('onfocusout', '$(this).trigger(\'submit\')');
    $form->getFormElement()->setAttribute('onfocusin', 'clearAjaxFormTimeout(this)');

?>
<?php if(! __AJAX__){ ?>
    <div class="card card--order card--ardress">
<?php } ?>
    <div class="card__content>">
        <h3>Uw Adres</h3>
        <?=$form?>
    </div>

<?php if(! __AJAX__){ ?>
    </div>
<?php } ?>
