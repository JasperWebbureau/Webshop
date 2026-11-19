<?php
use Flexgrid\Utils\Files\ImageFile;
?>
<div class="webshop-product-gallery">
    <?php foreach ($images as $source) { $image = ImageFile::getImageFile($source); ?>
        <?php if ($image->exists()) { ?>
            <a href="<?=htmlspecialchars($image->getResizeUrl(1600, 1200, 1), ENT_QUOTES, 'UTF-8')?>" target="_blank" rel="noopener">
                <img src="<?=htmlspecialchars($image->getResizeUrl(960, 720, 1), ENT_QUOTES, 'UTF-8')?>" alt="<?=htmlspecialchars($title, ENT_QUOTES, 'UTF-8')?>" loading="lazy">
            </a>
        <?php } ?>
    <?php } ?>
</div>
