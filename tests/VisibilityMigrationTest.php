<?php

if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../tools/bootstrap.php';
require __DIR__ . '/../tools/migrate-visibility.php';

use Flexgrid\Database\Connection;

if (!$webshopDatabaseIsLocal) { throw new RuntimeException('Local temporary fixtures only.'); }
$db = Connection::getConnections();
foreach (['webshopproduct', 'webshopproductgroup', 'webshopproductmaingroup', 'webshopshippingmethod', 'webshoptaxrate', 'webshopdiscountcode', 'webshopmoodboard'] as $table) {
    $schema = $db->query('SHOW CREATE TABLE `' . $table . '`')->fetch(PDO::FETCH_NUM)[1];
    $db->exec(preg_replace('/^CREATE TABLE/', 'CREATE TEMPORARY TABLE', $schema));
    if (!$db->query('SHOW COLUMNS FROM `' . $table . '` LIKE \'is_active\'')->fetchColumn()) {
        $db->exec('ALTER TABLE `' . $table . '` ADD COLUMN is_active TINYINT NULL');
    }
}
$db->exec("INSERT INTO webshopproduct (id,title,is_hidden,is_active,status) VALUES
    (990001,'Existing visible product',0,0,NULL),
    (990002,'Explicit draft',0,1,'draft'),
    (990003,'Hidden published product',1,1,'published')");
$before = $db->query('SELECT id,is_hidden FROM webshopproduct ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
$backup = sys_get_temp_dir() . '/webshop-visibility-' . bin2hex(random_bytes(8)) . '.json';
try {
    $migration = new WebshopVisibilityMigration($db);
    $result = $migration->apply($backup);
    if ($result['published_products'] !== 1 || count($result['removed_columns']) !== 7) { throw new RuntimeException('Migration result differs from the planned legacy fields.'); }
    if ($before !== $db->query('SELECT id,is_hidden FROM webshopproduct ORDER BY id')->fetchAll(PDO::FETCH_ASSOC)) { throw new RuntimeException('Existing hidden flags changed.'); }
    $statuses = $db->query('SELECT id,status FROM webshopproduct ORDER BY id')->fetchAll(PDO::FETCH_KEY_PAIR);
    if ($statuses !== [990001 => 'published', 990002 => 'draft', 990003 => 'published']) { throw new RuntimeException('Explicit publication states were changed.'); }
    $plan = $migration->inspect();
    if ($plan['legacy_columns'] || $plan['empty_product_statuses']) { throw new RuntimeException('Second inspection must be empty.'); }
    $snapshot = json_decode(file_get_contents($backup), true, 512, JSON_THROW_ON_ERROR);
    if (count($snapshot['legacy_columns']['webshopproduct']['rows']) !== 3) { throw new RuntimeException('Flag backup is incomplete.'); }
    echo "Visibility migration verified: hidden flags and drafts preserved, blank status repaired, seven legacy columns removed, rerun empty. Temporary tables only.\n";
} finally { if (file_exists($backup)) { unlink($backup); } }
