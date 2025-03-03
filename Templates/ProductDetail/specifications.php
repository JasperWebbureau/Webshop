<?php
/**
 * @var $product \App\Entity\Product
 */


$fields = $product->getFields();
$specifications = [];
foreach($fields as $field){
    //
    if(strpos($field['roles'], 'specification')!== false){

        $specifications[] = $field;
    };
}
?>


<div class="product_detail__specification">
    <ul>
        <?php

        foreach($specifications as $field){

            $title= $field['spec_title'] ?? $field['name'];
            $valueGetter = $field['getter_name'];
            $value = $product->$valueGetter();
            if($value != '' && $title != ''){  ?>
            <li> <?=$title?> :<?=$value?> </li>

        <?php }

        }
        ?>
    </ul>

</div>



