<?php
/**
 * @var $product \App\Entity\Product

 */
$formname = 'cart';
$cart = App\Webshop\Cart\Cart::getInstance();

$idInput = new \Form\Input\HiddenInput('id',$formname);
$idInput->setValue($product->getId());


$amountInput = new \Form\Input\StepperInput('amount',$formname);
new \Form\Form('cart');
$max= $product->getStock() ?: 99;
$amountInput->setMax($max)->setMin(1)->setValue(1);
$amountInput->getWrapperElement()->addStyle('--cw', '5');

$event = (new \Event\AjaxEvent('App\Webshop\Cart\Cart', 'addArticle' ));

$options = $product->getOptions();
$colorRepo = new \App\Repository\ColorRepository();
?>
<div class="product_detail__add">
    <div class="">
        <h2>Bestellen</h2>
    </div>
    <div class="add">
        <form name="cart" action="<?=$event->getName()?>" method="ajax" ajax="true">
            <?=$idInput?>
            <?=$amountInput?>
            <div class="price align-center" style="--cw:6" data-price="<?=$product->getPrice()?>">
                <span><?=$product->getPriceHtml()?></span>
            </div>
            <?php if(!empty($options)) {?>
                <div class="">
                    <?php foreach($options['fields'] as $name=> $field){
                        $getterName = $field['getter_name'];
                        $html = '';
                        ob_start();
                        foreach($options['children'] as $child){
                            /**
                             * @var $child \App\Entity\Product
                             */
                            $value = $child->$getterName();
                            if($value == ''){
                                continue;
                            }
                            if($name == 'kleur') {

                                $color = $colorRepo->findById($value);
                                $color->getColor();

                                ?>
                                <a class="add__option" href="<?=$child->getDetailUrl(46)?>"><div class="color" style="background-color:<?=$color->getColor()?>"></div> <?=$color->getTitle()?> </a>
                                <?php
                            }else{
                                ?>
                                <a class="add__option" href="<?=$child->getDetailUrl(46)?>"><?=$child->$getterName()?></a>

                                <?php
                            }

                        }
                        $html = ob_get_clean();
                        if($html != '' ){?>
                            <h4><?=$name ?></h4>
                            <div class="add__options"><?=$html?>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            <?php }?>
            <button type="submit" style="--cw:7;" class="button button-primary">
                Voeg aan winkelwagen toe
            </button>
        </form>
    </div>
</div>