<?php

if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../tools/bootstrap.php';

use Flexgrid\Modules\Webshop\Entity\WebshopProduct;
use Flexgrid\Modules\Webshop\Entity\WebshopProductGroup;
use Flexgrid\Modules\Webshop\Entity\WebshopProductMainGroup;
use Flexgrid\Modules\Webshop\Service\PriceService;
use Flexgrid\Response\TemplateResponse;
use Flexgrid\Response\Assets\AssetManager;
use Flexgrid\Utils\Files\ImageFile;

final class CatalogFixtureProduct extends WebshopProduct
{
    public $variants = [];
    public function getLinkedProductsEntities() { return $this->variants; }
    public function getDetailUrl($pageId = 0, $addGroup = true) { return '#fixture-product'; }
    public function getWebshopProductGroupParent() { return null; }
}
final class CatalogFixtureGroup extends WebshopProductGroup
{
    public function getDetailUrl($pageId = 0, $addGroup = true) { return '#fixture-group'; }
    public function getImage() { return ImageFile::getImageFile($this->image); }
}
final class CatalogFixtureMainGroup extends WebshopProductMainGroup
{
    public function getDetailUrl($pageId = 0, $addGroup = true) { return '#fixture-main-group'; }
    public function getImage() { return ImageFile::getImageFile($this->image); }
}
$checks = 0;
$assert = static function ($condition, $message) use (&$checks) { ++$checks; if (!$condition) { throw new RuntimeException($message); } };
$assertCardNavigation = static function ($rendered, $card, $hasFavorite = false) use ($assert) {
    $document = new DOMDocument();
    $document->loadHTML('<?xml encoding="UTF-8">' . $rendered, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
    $xpath = new DOMXPath($document);
    $assert($xpath->query('//article[contains(concat(" ", normalize-space(@class), " "), " clickable ")]')->length === 1, 'One clickable card: ' . $card);
    $assert($xpath->query('//article//a[@href]')->length === 1, 'Exactly one keyboard-accessible primary link: ' . $card);
    $assert($xpath->query('//h3//a | //a//h3 | //a//img')->length === 0, 'Title and image are not wrapped in links: ' . $card);
    $assert($xpath->query('//article/div[contains(concat(" ", normalize-space(@class), " "), " card__media ")]')->length === 1, 'Media belongs directly to the card: ' . $card);
    if ($hasFavorite) {
        $assert($xpath->query('//button[@data-webshop-product-card2-favorite and not(ancestor::a)]')->length === 1, 'Favorite remains a separate button: ' . $card);
    }
};
$product = (new CatalogFixtureProduct())->setId(923456)->setTitle('Een lange producttitel met <tekst> & details')
    ->setShortDescription('<p>Een beschrijving met HTML en details.</p>')->setPrice(121)->setSalePrice(99)
    ->setStock(3)->setTrackStock(1)->setImage('');
$pricing = (new PriceService())->getCardPricing($product);
$assert($pricing['price'] === 99.0 && $pricing['sale'], 'Single sale price');
$variant = (new CatalogFixtureProduct())->setId(923457)->setPrice(80);
$product->variants = [$variant];
$pricing = (new PriceService())->getCardPricing($product);
$assert($pricing['price'] === 80.0 && $pricing['from'] && !$pricing['sale'], 'Cluster minimum does not display a misleading original price');
$variant->setIsHidden(1);
$assert(count($product->getVariantClusterProducts(true)) === 1, 'Hidden sibling is hidden');
$product->variants = [];
AssetManager::reset();
$html = '';
$categoryHtml = '';
foreach (['ProductCard', 'ProductCard2', 'ProductCardCompact'] as $card) {
    $rendered = (string)new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/Cards/' . $card . '.php', [
        'entity' => $product, 'pageId' => 48, 'cardWidth' => 4,
    ]);
    $assert(strpos($rendered, '&lt;tekst&gt;') !== false, 'Escaped title in ' . $card);
    $assert(strpos($rendered, 'href="#fixture-product"') !== false, 'Keyboard-accessible product link in ' . $card);
    $assert(strpos($rendered, 'card__media') !== false, 'Shared card structure in ' . $card);
    $assertCardNavigation($rendered, $card, true);
    $html .= $rendered;
}
$sampleImages = glob('Files/Images/WebshopProduct/*.webp');
$sampleImage = $sampleImages[0] ?? '';
foreach (['ProductMainGroupCard', 'ProductGroupCard', 'ProductGroupCard2'] as $card) {
    $category = $card === 'ProductMainGroupCard' ? new CatalogFixtureMainGroup() : new CatalogFixtureGroup();
    $category->setTitle('Categorie met <tekst> & een lange titel')
        ->setDescription('<p>Een beschrijving met &lt;tekst&gt; en meer informatie over deze categorie.</p>')->setImage('');
    if ($category instanceof CatalogFixtureMainGroup) { $category->setIntroSentence('Een korte introductie'); }
    $render = static function ($entity) use ($card) {
        return (string)new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/Cards/' . $card . '.php', [
            'entity' => $entity, 'pageId' => 48, 'cardWidth' => 4,
        ]);
    };
    $rendered = $render($category);
    $assert(strpos($rendered, '&lt;tekst&gt;') !== false, 'Escaped category title in ' . $card);
    $assert(strpos($rendered, 'card__media') !== false && strpos($rendered, 'card__content') !== false, 'Shared category structure in ' . $card);
    $assert(strpos($rendered, 'fa-box-open') !== false, 'Missing category image in ' . $card);
    $assert(strpos($rendered, 'webshop-card-accent:') === false, 'Category colour follows theme in ' . $card);
    $assert(strpos($rendered, 'href="#fixture-') !== false, 'Keyboard-accessible category in ' . $card);
    $assertCardNavigation($rendered, $card);
    if ($sampleImage !== '') {
        $category->setImage($sampleImage);
        $rendered = $render($category);
        $assert(strpos($rendered, '<img ') !== false, 'Category image in ' . $card);
        $assertCardNavigation($rendered, $card);
    }
    $categoryHtml .= $rendered;
}
if ($sampleImage !== '') {
    $product->setImage($sampleImage);
    foreach (['ProductCard', 'ProductCard2', 'ProductCardCompact'] as $card) {
        $rendered = (string)new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/Cards/' . $card . '.php', [
            'entity' => $product, 'pageId' => 48, 'cardWidth' => 4,
        ]);
        $assert(strpos($rendered, '<img ') !== false, 'Product image in ' . $card);
        $assertCardNavigation($rendered, $card, true);
        $html .= $rendered;
    }
    $product->setImage('');
}
$searchCard = (string)new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/Cards/SearchResultCard.php', [
    'entity' => $product, 'pageId' => 48,
]);
$assertCardNavigation($searchCard, 'SearchResultCard');
$html .= $searchCard;
$detail = (string)new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/ProductGrid/ProductGridDetail.php', [
    'entity' => $product, 'pageId' => 48, 'relatedProducts' => [],
]);
$assert(strpos($detail, 'webshop-product-detail__image-fallback') !== false, 'Product detail renders without image and App integrations');
$assets = AssetManager::getAssets();
$paths = array_map(static function ($file) { return $file->getPathFromRoot(); }, $assets['less'] ?? []);
$shared = 'Flexgrid/Modules/Webshop/src/Templates/ProductCard/Css/ProductCardContent.scss';
$assert(count(array_filter($paths, static function ($path) use ($shared) { return $path === $shared; })) === 1, 'Shared card CSS loads once');
foreach (['ProductCard', 'ProductCard2', 'ProductCardCompact', 'ProductMainGroupCard', 'ProductGroupCard', 'ProductGroupCard2', 'SearchResultCard'] as $card) {
    $assert(in_array('Flexgrid/Modules/Webshop/src/Templates/Cards/Css/' . $card . '.scss', $paths, true), 'Variant stylesheet registered: ' . $card);
}
$assert(count($paths) === count(array_unique($paths)), 'Repeated cards register their assets once');
// Compile the actual runtime styles using the same mixins and compiler as LessFile.
$compiler = new \ScssPhp\ScssPhp\Compiler();
$mixins = file_get_contents('Flexgrid/Flexgrid/src/Assets/Css/Mixins.scss') . file_get_contents('Files/Assets/Css/Mixins.scss');
$css = '';
foreach (array_merge(['Files/Assets/Css/Variables.scss', 'Files/Assets/Css/styleguide.less', 'Files/Assets/Css/buttons.less', 'Files/Assets/Css/cards.less', 'Files/Assets/Css/Main.scss', 'Files/Assets/Css/Header.scss', 'Files/Assets/Css/main.less'], $paths) as $path) {
    $compiled = $compiler->compileString($mixins . file_get_contents($path))->getCss();
    ++$checks; // Empty variant CSS is valid; compiler errors still fail this run.
    $css .= $compiled;
}
$iconStyle = '';
if (is_file('Templates/Main/Head.php') && preg_match('~<link\b[^>]+href="[^"]*/font-awesome/[^"]*"[^>]*>~', file_get_contents('Templates/Main/Head.php'), $matches)) {
    $iconStyle = $matches[0]; // Use the project's existing icon stylesheet in this local preview.
}
file_put_contents('tmp/webshop-card-preview.html', '<!doctype html><html lang="nl"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">' . $iconStyle . '<style>'
    . $css . '</style><body><main style="padding:32px 0"><grid><div style="--cw:12"><h1>Standaard webshopkaarten</h1><h2>Hoofdgroepen en productgroepen</h2></div></grid><grid>' . $categoryHtml
    . '</grid><grid style="margin-top:40px"><div style="--cw:12"><h2>Producten: uitgebreid, visueel en compact</h2></div></grid><grid>' . $html . '</grid></main></body></html>');
echo $checks . " catalog checks passed; actual templates and assets rendered.\n";
