<?php
/**
 * @var $entity \Flexgrid\Modules\Webshop\Entity\WebshopProduct
 */

use App\Image\Controller\ImageController;
use App\Review\Repository\ReviewRepository;
use App\Usp\Controller\UspController;
use Flexgrid\Modules\Webshop\Service\FavoriteService;
use Flexgrid\Response\TemplateResponse;
use Flexgrid\Modules\Webshop\Controller\WebshopController;

$product = $entity;
$pageId = $pageId ?? 0;
$price = (float)$product->getPrice();
$salePrice = (float)$product->getSalePrice();
$activePrice = $salePrice > 0 && $salePrice < $price ? $salePrice : $price;
$groupTitle = '';
$mainGroupTitle = '';
$manufacturer = trim((string)$product->getManufacturer());
$isPublished = (int)$product->getIsActive() === 1 && (string)$product->getStatus() === 'published';
$isInStock = (int)$product->getTrackStock() !== 1 || (int)$product->getStock() > 0;
$isAvailable = $isPublished && $isInStock;
$productGridUrl = '';
$image = $product->getImage();
$highlightImage = method_exists($product, 'getHighlightImage') ? $product->getHighlightImage() : null;
if(! $highlightImage->exists()){
    $highlightImage = $image;
}
$highlightText = method_exists($product, 'getHighlightText') ? trim((string)$product->getHighlightText()) : '';
$galleryImages = [];
$relatedProducts = $relatedProducts ?? [];
$favoriteService = new FavoriteService();
$isFavorite = $favoriteService->hasProduct((int)$product->getId());
$reviewSummary = (new ReviewRepository())->getProductSummary((int)$product->getId());
$reviewCount = (int)$reviewSummary['count'];
$reviewRounded = max(0, min(5, (int)$reviewSummary['rounded']));
$reviewStars = str_repeat('&#9733;', $reviewRounded) . str_repeat('&#9734;', 5 - $reviewRounded);
$reviewLabel = $reviewCount === 1
    ? t('webshop_product_detail_review_single', '1 review')
    : sprintf(t('webshop_product_detail_review_count', '%s reviews'), $reviewCount);
if ($reviewCount === 0) {
    $reviewLabel = t('webshop_product_detail_no_reviews', 'Nog geen reviews');
}

if ((int)$image->getMediaId() > 0 || trim((string)$image) !== '') {
    $galleryImages[] = $image;
}

foreach (array_filter(array_map('trim', explode(',', (string)$product->getImages()))) as $imageId) {
    $galleryImages[] = $imageId;
}

$variantProducts = method_exists($product, 'getVariantClusterProducts') ? $product->getVariantClusterProducts(true) : [$product];
$variantColors = [];
$variantSizes = [];

foreach ($variantProducts as $variantProduct) {
    $variantColor = trim((string)$variantProduct->getColor());
    $variantSize = trim((string)$variantProduct->getSize());

    if ($variantColor !== '') {
        $variantColors[$variantColor] = true;
    }

    if ($variantSize !== '') {
        $variantSizes[$variantSize] = true;
    }
}

$variantOptionMode = count($variantColors) > 1 ? 'color' : (count($variantSizes) > 1 ? 'size' : 'title');
$variantOptions = [];

foreach ($variantProducts as $variantProduct) {
    $optionKey = (string)$variantProduct->getId();

    if ($variantOptionMode === 'color' && trim((string)$variantProduct->getColor()) !== '') {
        $optionKey = strtolower(trim((string)$variantProduct->getColor()));
    } elseif ($variantOptionMode === 'size' && trim((string)$variantProduct->getSize()) !== '') {
        $optionKey = strtolower(trim((string)$variantProduct->getSize()));
    }

    if (!isset($variantOptions[$optionKey]) || (int)$variantProduct->getId() === (int)$product->getId()) {
        $variantOptions[$optionKey] = $variantProduct;
    }
}

$variantOptions = array_values($variantOptions);

if (method_exists($product, 'getWebshopProductGroupParent')) {
    $group = $product->getWebshopProductGroupParent();
    if ($group && method_exists($group, 'getTitle')) {
        $groupTitle = (string)$group->getTitle();
    }

    if ($group && method_exists($group, 'getWebshopProductMainGroupValue')) {
        $mainGroupTitle = (string)$group->getWebshopProductMainGroupValue();
    }
}

try {
    $productGridUrl = WebshopController::getProductGridUrl();
} catch (\Throwable $exception) {
    $productGridUrl = '';
}

$specs = [];
if ($groupTitle !== '') {
    $specs[t('webshop_product_detail_group', 'Productgroep')] = $groupTitle;
}
if ($manufacturer !== '') {
    $specs[t('webshop_product_detail_manufacturer', 'Merk')] = $manufacturer;
}
if (trim((string)$product->getSku()) !== '') {
    $specs[t('webshop_product_detail_sku', 'SKU')] = (string)$product->getSku();
}
if (trim((string)$product->getColor()) !== '') {
    $specs[t('webshop_product_detail_color', 'Kleur')] = (string)$product->getColor();
}
if (trim((string)$product->getSize()) !== '') {
    $specs[t('webshop_product_detail_size', 'Maat')] = (string)$product->getSize();
}
if ((float)$product->getTaxRate() > 0) {
    $specs[t('webshop_product_detail_tax', 'BTW')] = number_format((float)$product->getTaxRate(), 2, ',', '.') . '%';
}

$breadcrumbController = new \App\Block\Controller\NavController();
$breadcrumbs = $breadcrumbController->BreadCrumbs();
/*
$breadcrumbs = array_filter([
    t('webshop_product_detail_home', 'Home'),
    $mainGroupTitle,
    $groupTitle,
]);
*/
?>
<section class="webshop-product-detail">

    <div class="webshop-product-detail__inner">
        <?=$breadcrumbs?>

        <div class="webshop-product-detail__hero">
            <div class="webshop-product-detail__gallery">
                <?php if (!empty($galleryImages)) { ?>
                    <?=(new ImageController())->DetailGallery($galleryImages, (string)$product->getTitle(), 'webshop-product-' . (int)$product->getId())?>
                <?php } else { ?>
                    <div class="webshop-product-detail__image-fallback">
                        <i class="fas fa-box-open"></i>
                    </div>
                <?php } ?>
            </div>

            <aside class="webshop-product-detail__panel">
                <div class="webshop-product-detail__eyebrow">
                    <?=htmlspecialchars(strtoupper($groupTitle !== '' ? $groupTitle : $manufacturer), ENT_QUOTES, 'UTF-8')?>
                </div>

                <h1><?=htmlspecialchars((string)$product->getTitle(), ENT_QUOTES, 'UTF-8')?></h1>

                <div class="webshop-product-detail__rating" aria-label="<?=t('webshop_product_detail_rating', 'Beoordeling')?>">
                    <span aria-hidden="true"><?=$reviewStars?></span>
                    <small><?=$reviewLabel?></small>
                </div>

                <div class="webshop-product-detail__price">
                    <?php if ($salePrice > 0 && $salePrice < $price) { ?>
                        <span class="webshop-product-detail__price-old">&euro; <?=number_format($price, 2, ',', '.')?></span>
                    <?php } ?>
                    <span class="webshop-product-detail__price-current">&euro; <?=number_format($activePrice, 2, ',', '.')?></span>
                </div>

                <?php if (trim((string)$product->getShortDescription()) !== '') { ?>
                    <div class="webshop-product-detail__intro text-block">
                        <?=nl2br(htmlspecialchars((string)$product->getShortDescription(), ENT_QUOTES, 'UTF-8'))?>
                    </div>
                <?php } ?>

                <div class="webshop-product-detail__availability <?=$isAvailable ? 'is-available' : 'is-unavailable'?>">
                    <span></span>
                    <?php if (!$isPublished) { ?>
                        <?=t('webshop_product_not_available', 'Niet beschikbaar')?>
                    <?php } else { ?>
                        <?=$isInStock ? t('webshop_product_available_shipping', 'Op voorraad binnen 2-3 werkdagen verzonden') : t('webshop_product_unavailable', 'Niet op voorraad')?>
                    <?php } ?>
                </div>

                <?php if (count($variantOptions) > 1) { ?>
                    <div class="webshop-product-detail__variants">
                        <span><?=t('webshop_product_detail_variants', 'Opties')?></span>
                        <div>
                            <?php foreach ($variantOptions as $variantProduct) {
                                $optionLabel = (string)$variantProduct->getTitle();
                                if ($variantOptionMode === 'color' && trim((string)$variantProduct->getColor()) !== '') {
                                    $optionLabel = trim((string)$variantProduct->getColor());
                                } elseif ($variantOptionMode === 'size' && trim((string)$variantProduct->getSize()) !== '') {
                                    $optionLabel = trim((string)$variantProduct->getSize());
                                }
                                ?>
                                <a
                                    class="<?=(int)$variantProduct->getId() === (int)$product->getId() ? 'is-selected' : ''?>"
                                    href="<?=$variantProduct->getDetailUrl((int)$pageId)?>"
                                >
                                    <?=htmlspecialchars($optionLabel, ENT_QUOTES, 'UTF-8')?>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>

                <div class="webshop-product-detail__actions">
                    <?=new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/AddToCart/AddToCart.php', [
                        'product' => $product,
                        'showQuantity' => true,
                    ])?>
                </div>

                <?=new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/ProductGrid/FavoriteButton.php', [
                    'product' => $product,
                    'isFavorite' => $isFavorite,
                ])?>

                <?=(new UspController())->productDetail(3)?>
            </aside>
        </div>

        <?php if ($highlightText !== '' || ($highlightImage && ((int)$highlightImage->getMediaId() > 0 || trim((string)$highlightImage) !== ''))) { ?>
            <div class="webshop-product-detail__highlight">
                <div class="webshop-product-detail__highlight-image">
                    <?php if ($highlightImage && ((int)$highlightImage->getMediaId() > 0 || trim((string)$highlightImage) !== '')) { ?>
                        <img src="<?=$highlightImage->getResizeUrl(980, 760, 1)?>" alt="<?=htmlspecialchars((string)$product->getTitle(), ENT_QUOTES, 'UTF-8')?>" loading="lazy">
                    <?php } ?>
                </div>
                <div class="webshop-product-detail__highlight-copy text-block">
                    <?=$highlightText !== '' ? $highlightText : '<h2>' . htmlspecialchars((string)$product->getTitle(), ENT_QUOTES, 'UTF-8') . '</h2>'?>
                </div>
            </div>
        <?php } ?>

        <div class="webshop-product-detail__info">
            <?php if (!empty($specs)) { ?>
                <details open>
                    <summary><?=t('webshop_product_detail_specs_title', 'Productdetails')?></summary>
                    <dl class="webshop-product-detail__specs">
                        <?php foreach ($specs as $label => $value) { ?>
                            <div>
                                <dt><?=htmlspecialchars((string)$label, ENT_QUOTES, 'UTF-8')?></dt>
                                <dd><?=htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8')?></dd>
                            </div>
                        <?php } ?>
                    </dl>
                </details>
            <?php } ?>

            <?php if (trim(strip_tags((string)$product->getDescription())) !== '') { ?>
                <details open>
                    <summary><?=t('webshop_product_detail_description_title', 'Omschrijving')?></summary>
                    <div class="webshop-product-detail__description text-block">
                        <?=$product->getDescription()?>
                    </div>
                </details>
            <?php } ?>

            <details>
                <summary><?=t('webshop_product_detail_shipping_returns', 'Verzending & retourneren')?></summary>
                <div class="webshop-product-detail__description text-block">
                    <p><?=t('webshop_product_detail_shipping_returns_text', 'Bestellingen worden zorgvuldig verpakt en zo snel mogelijk verzonden. Retourneren kan binnen 30 dagen.')?></p>
                </div>
            </details>
        </div>

        <?php if (!empty($relatedProducts)) { ?>
            <section class="webshop-product-detail__related">
                <h2><?=t('webshop_product_detail_related_title', 'Mooi erbij')?></h2>
                <div class="webshop-product-detail__related-grid">
                    <?php foreach ($relatedProducts as $relatedProduct) {
                        $relatedImage = $relatedProduct->getImage();
                        $relatedPrice = (float)$relatedProduct->getSalePrice() > 0 && (float)$relatedProduct->getSalePrice() < (float)$relatedProduct->getPrice()
                            ? (float)$relatedProduct->getSalePrice()
                            : (float)$relatedProduct->getPrice();
                        ?>
                        <a class="webshop-product-detail__related-card" href="<?=$relatedProduct->getDetailUrl((int)$pageId)?>">
                            <?php if ((int)$relatedImage->getMediaId() > 0 || trim((string)$relatedImage) !== '') { ?>
                                <img src="<?=$relatedImage->getResizeUrl(520, 420, 1)?>" alt="<?=htmlspecialchars((string)$relatedProduct->getTitle(), ENT_QUOTES, 'UTF-8')?>" loading="lazy">
                            <?php } else { ?>
                                <span class="webshop-product-detail__related-fallback"><i class="fas fa-box-open"></i></span>
                            <?php } ?>
                            <strong><?=htmlspecialchars((string)$relatedProduct->getTitle(), ENT_QUOTES, 'UTF-8')?></strong>
                            <small>&euro; <?=number_format($relatedPrice, 2, ',', '.')?></small>
                        </a>
                    <?php } ?>
                </div>
            </section>
        <?php } ?>
    </div>
</section>
