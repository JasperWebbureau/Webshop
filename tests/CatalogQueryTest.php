<?php

if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../tools/bootstrap.php';

use Flexgrid\Database\Connection;
use Flexgrid\Modules\Webshop\Repository\WebshopProductRepository;

if (!$webshopDatabaseIsLocal) { throw new RuntimeException('Local temporary fixtures only.'); }
$db = Connection::getConnections();
$schema = $db->query('SHOW CREATE TABLE webshopproduct')->fetch(PDO::FETCH_NUM)[1];
$db->exec(preg_replace('/^CREATE TABLE/', 'CREATE TEMPORARY TABLE', $schema));
$insert = $db->prepare('INSERT INTO webshopproduct (id,title,price,group_id,status,is_hidden) VALUES (?,?,?,?,?,?)');
for ($id = 1; $id <= 8; ++$id) { $insert->execute([990000 + $id, 'Fixture ' . $id, 10, 7, 'published', 0]); }
$insert->execute([990020, 'Draft', 10, 7, 'draft', 0]);
$insert->execute([990022, 'Hidden', 10, 7, 'published', 1]);
$insert->execute([990023, 'Other group', 10, 8, 'published', 0]);
$where = "group_id = 7 AND status = 'published' AND (is_hidden = 0 OR is_hidden IS NULL)";
$repo = new WebshopProductRepository();
$first = $repo->getCatalogPage(3, $where, 'title:ASC', 0);
$second = $repo->getCatalogPage(3, $where, 'title:ASC', 1);
$last = $repo->getCatalogPage(3, $where, 'title:ASC', 999);
$ids = static function ($result) { return array_map(static function ($product) { return (int)$product->getId(); }, $result['entities']); };
if ($first['pagination']['records'] !== 8 || $first['pagination']['totalPagesAvailable'] !== 3
    || count($first['entities']) !== 3 || count($second['entities']) !== 3 || count($last['entities']) !== 2
    || array_intersect($ids($first), $ids($second)) || $last['pagination']['currentPage'] !== 2) {
    throw new RuntimeException('Publication, group scope or pagination is incorrect.');
}
foreach (array_merge($first['entities'], $second['entities'], $last['entities']) as $product) {
    if ($product->getStatus() !== 'published' || (int)$product->getIsHidden() !== 0) { throw new RuntimeException('Unavailable product leaked into catalog.'); }
}
echo "Catalog publication, group scope and all three pages verified with temporary products.\n";
