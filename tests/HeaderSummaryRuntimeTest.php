<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../tools/bootstrap.php';

use Flexgrid\App\Settings\Settings;
use Flexgrid\Database\Connection;
use Flexgrid\Modules\Webshop\Controller\WebshopProductController;
use Flexgrid\Modules\Webshop\Entity\WebshopProduct;
use Flexgrid\Repository\PageRepository;
use Flexgrid\Response\Assets\AssetManager;
use Flexgrid\Response\TemplateResponse;

if (!$webshopDatabaseIsLocal) { throw new RuntimeException('Local temporary fixtures only.'); }
$db = Connection::getConnections();
$table = (new PageRepository())->getTable();
if ($table !== 'page') { throw new RuntimeException('Unexpected page fixture table.'); }
$schema = $db->query('SHOW CREATE TABLE `page`')->fetch(PDO::FETCH_NUM)[1];
$db->exec(preg_replace('/^CREATE TABLE/', 'CREATE TEMPORARY TABLE', $schema));
$insert = $db->prepare('INSERT INTO `page` (`id`, `title`, `url`) VALUES (?, ?, ?)');
new Settings();
foreach (['cart', 'favorites', 'checkout'] as $index => $name) {
    $id = 990001 + $index;
    $insert->execute([$id, $name, '/fixture-' . $name]);
    Settings::set('page_webshop_' . $name . '_id', $id);
}
Settings::set('page_product_grid_page_id', 990004); // Memory only; never save fixture settings.
final class HeaderSummaryFixtureProduct extends WebshopProduct
{
    public function getDetailUrl($pageId = 0, $addGroup = true) { return '#fixture-product'; }
    public function getWebshopProductGroupValue($getter = null) { return 'Voorbeeldgroep'; }
}
$product = (new HeaderSummaryFixtureProduct())->setId(923456)->setTitle('Product <tekst> & details')->setImage('');
$all = ['CartSummary', 'FavoriteSummary', 'SearchBar'];
$mode = $argv[1] ?? 'all';
if ($mode !== 'all' && !in_array($mode, $all, true)) { throw new RuntimeException('Unknown variant.'); }
$templates = $mode === 'all' ? $all : [$mode];
$checks = 0;
$assert = static function ($condition, $message) use (&$checks) {
    ++$checks;
    if (!$condition) { throw new RuntimeException($message); }
};
AssetManager::reset();
foreach (['Files/Assets/Css/Variables.scss', 'Files/Assets/Css/styleguide.less', 'Files/Assets/Css/buttons.less',
    'Files/Assets/Css/Main.scss', 'Files/Assets/Css/Header.scss', 'Templates/Pages/Header/Css/main.less', 'App/Block/Templates/Navigation/MainNav/Css/main.scss'] as $path) {
    AssetManager::add($path);
}
$html = '';
foreach ($templates as $template) {
    foreach ([0, 4] as $quantity) {
        if ($template === 'SearchBar' && $quantity > 0) { continue; }
        $items = $quantity > 0 ? array_fill(0, 4, $product) : [];
        if ($template === 'CartSummary') {
            $items = array_map(static function ($product) { return ['product' => $product, 'quantity' => 2, 'unit_price' => 12.50]; }, $items);
        }
        $args = ['items' => $items, 'summary' => ['quantity' => $quantity, 'subtotal_formatted' => '? 100,00'],
            'eventClass' => WebshopProductController::class, 'pageId' => 990004, 'card' => 'SearchResultCard', 'limit' => 5];
        $rendered = (string)new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/' . $template . '/' . $template . '.php', $args);
        $assert(substr_count($rendered, 'class="webshop-header-summary__trigger') === 1, 'One shared trigger: ' . $template);
        $assert(strpos($rendered, 'webshop-header-summary__configuration-error') === false, 'Configured URLs resolve: ' . $template);
        $assert(strpos($rendered, 'tabindex="0"') === false, 'Only the real action is a tab stop: ' . $template);
        if ($template === 'SearchBar') {
            preg_match('/aria-controls="([^"]+)"/', $rendered, $match);
            $assert(isset($match[1]) && strpos($rendered, 'id="' . $match[1] . '"') !== false, 'Search controls match the dialog');
            $assert(strpos($rendered, 'data-webshop-search-trigger') !== false, 'Search JS hook remains present');
        } else {
            $hook = $template === 'CartSummary' ? 'js-webshop-cart-count' : 'js-webshop-favorite-count';
            $assert(strpos($rendered, $hook . '">' . $quantity . '</span>') !== false, 'Ajax counter hook and value: ' . $template);
            if ($quantity > 0) {
                $assert(strpos($rendered, '&lt;tekst&gt;') !== false, 'Escaped item title: ' . $template);
                $assert(substr_count($rendered, 'webshop-header-summary__item-content') === 3, 'Three preview items: ' . $template);
                $assert(strpos($rendered, 'webshop-header-summary__more') !== false, 'More-items indicator: ' . $template);
            } else {
                $assert(strpos($rendered, 'webshop-header-summary__empty') !== false, 'Empty state: ' . $template);
            }
        }
        if ($quantity === 0) { $html .= $rendered; }
    }
}
$assets = AssetManager::getAssets();
$paths = array_map(static function ($file) { return $file->getPathFromRoot(); }, $assets['less'] ?? []);
$shared = 'Flexgrid/Modules/Webshop/src/Templates/HeaderSummary/Css/HeaderSummaryTrigger.scss';
$assert(count(array_filter($paths, static function ($path) use ($shared) { return $path === $shared; })) === 1, 'Shared summary CSS exactly once, including standalone variants');
$assert(count($paths) === count(array_unique($paths)), 'No duplicate CSS paths');
$assert(array_search($shared, $paths, true) > array_search('Files/Assets/Css/Header.scss', $paths, true), 'Module CSS follows global project CSS');
$scripts = array_values($assets['js'] ?? []);
$assert(count($scripts) === count(array_unique($scripts)), 'No duplicate action scripts');
$mixins = file_get_contents('Flexgrid/Flexgrid/src/Assets/Css/Mixins.scss') . file_get_contents('Files/Assets/Css/Mixins.scss');
$compiler = new \ScssPhp\ScssPhp\Compiler();
$assert($compiler->compileString($mixins . file_get_contents('Flexgrid/Modules/Webshop/src/Templates/ProductMainGroupDropdown/Css/ProductMainGroupDropdown.scss'))->getCss() !== '', 'Dropdown colour context compiles');
$compiler = new \ScssPhp\ScssPhp\Compiler();
$css = '';
foreach ($paths as $path) { $css .= $compiler->compileString($mixins . file_get_contents($path))->getCss(); ++$checks; }
$assert(strpos($css, '---link-') === false, 'Link tokens have their documented names');
$assert(strpos($css, 'var(--link-hover-color, var(--link-color, var(--color)))') !== false, 'Hover falls back through link to base colour');
$assert(strpos($css, 'var(--custom-color, var(--link-color, var(--color)))') !== false, 'Explicit override has priority over default link colour');
$assert(strpos($css, 'var(--custom-font-size, var(--link-hover-font-size, var(--link-font-size, var(--font-size))))') !== false, 'Hover preserves base font tokens');
if ($mode === 'all') {
    $head = file_get_contents('Templates/Main/Head.php');
    preg_match('~<link\b[^>]+href="[^"]*/font-awesome/[^"]*"[^>]*>~', $head, $iconStyles);
    $iconStyles = $iconStyles[0] ?? '';
    $content = '<!doctype html><html lang="nl"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Webshop headercontrole</title>'
        . $iconStyles . '<style>' . $css . '</style><body><main><grid><div style="--cw:12;padding:30px 0"><h1>Header en shopacties</h1><p>Moduledefaults op wit, projectheader, sticky header en een expliciete kleuroverride.</p></div></grid>'
        . '<grid><div class="header__shop-actions" style="--cw:12;justify-content:flex-start;padding:30px 0">' . $html . '</div></grid>';
    foreach (['' => '', 'fixed-active' => '', 'preview-custom' => '--custom-color:#a21caf;--header-background:#fdf4ff;'] as $class => $style) {
        $content .= '<header style="margin:24px 0;' . $style . '"><section class="header__middle ' . $class . '" style="padding:24px 0"><grid><div style="--cw:3"><strong>Webshop</strong></div><div class="header__shop-actions" style="--cw:9;--cw-xs:12"><nav class="main-menu"><div class="nav__content"><ul><li><a href="#">Home</a></li><li class="is-active"><a href="#">Webshop</a></li></ul></div></nav>' . $html . '</div></grid></section></header>';
    }
    $content .= '</main></body></html>';
    file_put_contents('tmp/webshop-header-preview.html', $content);
}
echo $checks . ' header checks passed (' . $mode . ").\n";
