<?php
/**
 * @var $product \App\Entity\Product
 */


$fields = $product->getFields();
$imageFields = [];
foreach($fields as $field){
    if($field['type'] == 'image'){
        $imageFields[] = $field['getter_name'];
    };
}
?>


<div class="product_detail__image">

    <?php

    foreach($imageFields as $imageGetter){

        $image= $product->$imageGetter();

        if($image->getExtension()!= ''){

            $source = $image->getResizeUrl(630,520, 0);

            ?>
                <figure>
                    <img src="<?=$source?>" alt="<?=$product->getTitle()?>">
                </figure>
        <?php  }
    }
    ?>
</div>



