<?php
/**
 * @var $client \App\Webshop\Client\Client;
 */

    $form = $client->getForm('clientOrder');
    $event = new \Event\AjaxEvent('App\Webshop\Client\Client', 'edit');
    $form->addEvent($event);
    $form->getFormElement()->setAttribute('ajax', 'true');
    $form->getFormElement()->setAttribute('onfocusout', '$(this).trigger(\'submit\')');

    ?>

<?php if(! __AJAX__){ ?>
<div class="card card--order card--client">
    <?php } ?>
    <div class="card__content>">
        <h3>Uw gegevens</h3>
        <?=$form?>
    </div>
    <?php if(! __AJAX__){ ?>
</div>
<?php } ?>


