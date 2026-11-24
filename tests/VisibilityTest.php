<?php

if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../tools/bootstrap.php';

use Flexgrid\Database\Connection;
use Flexgrid\Modules\Webshop\Controller\WebshopProductMainGroupController;
use Flexgrid\Modules\Webshop\Controller\WebshopMoodboardController;
use Flexgrid\Modules\Webshop\Repository\WebshopProductRepository;
use Flexgrid\Modules\Webshop\Repository\WebshopShippingMethodRepository;
use Flexgrid\Modules\Webshop\Repository\WebshopTaxRateRepository;
use Flexgrid\Modules\Webshop\Repository\WebshopMoodboardRepository;
use Flexgrid\Modules\Webshop\Service\DiscountService;
use Flexgrid\Modules\Webshop\Service\TaxService;

if (!$webshopDatabaseIsLocal) { throw new RuntimeException('Local temporary fixtures only.'); }
$db = Connection::getConnections();
$checks = 0;
$assert = static function ($condition, $message) use (&$checks) { ++$checks; if (!$condition) { throw new RuntimeException($message); } };
foreach (['WebshopProduct', 'WebshopProductGroup', 'WebshopProductMainGroup', 'WebshopShippingMethod', 'WebshopTaxRate', 'WebshopDiscountCode', 'WebshopMoodboard'] as $entityName) {
    $table = strtolower($entityName);
    $schema = $db->query('SHOW CREATE TABLE `' . $table . '`')->fetch(PDO::FETCH_NUM)[1];
    $db->exec(preg_replace('/^CREATE TABLE/', 'CREATE TEMPORARY TABLE', $schema));
    $class = 'Flexgrid\\Modules\\Webshop\\Entity\\' . $entityName;
    $entity = new $class();
    $assert(!method_exists($entity, 'getIsActive') && !method_exists($entity, 'setIsActive'), $entityName . ' only inherits visibility');
    $assert(!$db->query('SHOW COLUMNS FROM `' . $table . '` LIKE \'is_active\'')->fetchColumn(), $entityName . ' works without legacy column');
    $properties = \Flexgrid\Autowire\AutowireEngine::getCache()[$class]['definition']['properties'];
    $assert(isset($properties['isHidden']) && !isset($properties['isActive']), $entityName . ' metadata uses inherited visibility');
}
$db->exec("INSERT INTO webshopproductmaingroup (id,title,is_hidden) VALUES (990001,'Visible main group',0),(990002,'Hidden main group',1),(990003,'Only hidden products',0)");
$db->exec("INSERT INTO webshopproductgroup (id,title,main_group_id,is_hidden) VALUES (991001,'Visible group',990001,0),(991002,'Hidden parent',990002,0),(991003,'No visible product',990003,0)");
$db->exec("INSERT INTO webshopproduct (id,title,group_id,price,is_hidden) VALUES
    (992001,'Visible product',991001,10,NULL),
    (992002,'Hidden product',991001,10,1),
    (992004,'Hidden parent product',991002,10,0),
    (992005,'Only hidden product',991003,10,1)");
$arguments = new ReflectionProperty(\Flexgrid\Response\Response::class, 'arguments');
$arguments->setAccessible(true);
$mainIds = static function () use ($arguments) {
    $response = (new WebshopProductMainGroupController())->productMainGroupGrid(48);
    return array_map(static function ($entity) { return (int)$entity->getId(); }, $arguments->getValue($response)['entities']);
};
$assert($mainIds() === [990001], 'Only the main group with a visible product is shown');
$banner = (new WebshopProductMainGroupController())->banner(990002);
$assert($arguments->getValue($banner)['entity'] === null, 'A direct hidden main-group banner receives no entity');
$generator = (new ReflectionClass(\Flexgrid\Modules\Webshop\Service\ProductAiGeneratorService::class))->newInstanceWithoutConstructor();
$factory = new ReflectionMethod($generator, 'createProductFromData');
$factory->setAccessible(true);
$generated = $factory->invoke($generator, ['title' => 'Generated fixture', 'groupId' => 991001], 'Fallback');
$assert($generated instanceof \Flexgrid\Modules\Webshop\Entity\WebshopProduct && $generated->getIsHidden() === 1, 'AI data mapping uses inherited visibility without relying on a fluent setter');
$products = new WebshopProductRepository();
$assert(count($products->getByGroupId(991001)) === 1, 'Hidden products are excluded');
$product = $products->findForStockUpdate(992001);
$product->setIsHidden(1);
$products->add($product);
$assert($mainIds() === [], 'Hiding the last product through the shared property removes the main group');
$product->setIsHidden(0);
$products->add($product);
$assert($mainIds() === [990001], 'Showing the product restores the main group');
$assert(count($products->search(['is_hidden' => '1'])) === 2, 'Admin can filter hidden products');
$db->exec("INSERT INTO webshopshippingmethod (id,title,is_hidden) VALUES (995001,'Visible shipping',0),(995002,'Hidden shipping',1)");
$assert(count((new WebshopShippingMethodRepository())->getActive()) === 1, 'Shipping uses inherited visibility');
$db->exec("INSERT INTO webshoptaxrate (id,title,rate,is_default,is_hidden) VALUES (995001,'Visible tax',21,1,0),(995002,'Hidden tax',9,1,1)");
$taxes = new WebshopTaxRateRepository();
$assert(count($taxes->getActive()) === 1 && $taxes->getDefault()->getId() === 995001, 'Tax selection ignores hidden rates');
$product->setTaxRateId(995002)->setTaxRate(21);
$assert((new TaxService())->getProductRate($product) === 21.0, 'Direct hidden tax reference uses product fallback');
$db->exec("INSERT INTO webshopdiscountcode (id,title,code,discount_type,value,is_hidden) VALUES (995001,'Visible discount','VISIBLE','percentage',10,0),(995002,'Hidden discount','HIDDEN','percentage',10,1)");
$assert((new DiscountService())->resolveDiscount('VISIBLE', 100)['success'], 'Visible discount applies');
$assert(!(new DiscountService())->resolveDiscount('HIDDEN', 100)['success'], 'Hidden discount is rejected');
$db->exec("INSERT INTO webshopmoodboard (id,title,is_hidden) VALUES (995001,'Visible moodboard',0),(995002,'Hidden moodboard',1)");
$assert(count((new WebshopMoodboardRepository())->getActive()) === 1, 'Moodboard selection uses visibility');
$assert((new WebshopMoodboardController())->moodboard(995002, 48) === '', 'A direct hidden moodboard block renders nothing');
echo $checks . " inherited visibility checks passed; main-group hide/show, products, shipping, tax, discounts and moodboards. Temporary tables only.\n";
