<?php

/**
 * @var $groups Array;
 * @var $group
 */
?>
<div class="grid">
    <?php
    foreach($products as $product){
        include 'productCard.php';
    }


    if($pagination['totalPagesAvailable'] > 1){
        $i = 0;
        $url = __DOMAIN__ . '/' . \Flexgrid\FlexGrid::$page->getUrl();

        if($pagination['currentPage'] <  $pagination['totalPagesAvailable'] - 1){
            $nextHref = $url . '?page='. ((int)$pagination['currentPage'] + 1);
        };
        ?>

        <div class="pagination">
            <?php while( $i <$pagination['totalPagesAvailable'] ){
                if($i == 0){
                    $href = $url;
                }else{
                    $href =$url. '?page=' .$i;
                }
                $class = '';

                if($pagination['currentPage'] == $i){
                    $class='is-active';
                }
                ?>
                <a href="<?=$href?>" class="button button-icon <?=$class?>"><?=$i?></a>

                <?php $i++; } ?>
            <?php if($nextHref != ''){ ?>
                <a href="<?=$nextHref?>" class="button">Volgende</a>
            <?php  } ?>
        </div>





    <?php }
    ?>



</div>