<?php
/**
 * @var $orders \App\Webshop\Webshop\Orders;
 */

$result = $orders->validateOrderData();
?>

<?php if(! __AJAX__){ ?>
<div class="card card--order card--order-button">
<?php } ?>

<?php if($result['result'] == 1){ ?>
    <a href="<?=__DOMAIN__ . '/'. $orders->getPlaceOrderPage()->getUrl()?>" class="button button-primary">
        Bestellen
    </a>
<?php }else{ ?>
    <?php foreach($result as $class=>$values) {
        if( ! empty ($values['errors']) ){
            foreach($values['errors'] as $key =>$value){ ?>
                <div class="message message--error">
                    <?= App\Webshop\Webshop\Orders::getErrorMessage($key, $value);?>
                </div>
            <?php }

        }

        ?>


    <?php }  ?>

<?php }  ?>


<?php if(! __AJAX__){ ?>
</div>
<?php } ?>
