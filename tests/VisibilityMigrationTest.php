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
if (!$db->query("SHOW COLUMNS FROM webshopproduct WHERE Field = 'status'")->fetchColumn()) {
    $db->exec('ALTER TABLE webshopproduct ADD COLUMN status VARCHAR(20) NULL');
}
$db->exec("INSERT INTO webshopproduct (id,title,is_hidden,is_active,status) VALUES
    (990001,'Existing visible product',0,0,NULL),
    (990002,'Explicit draft',0,1,'draft'),
    (990003,'Hidden published product',1,1,'published'),
    (990004,'Archived product',NULL,1,'archived'),
    (990005,'Visible published product',NULL,1,'published')");
$backup = sys_get_temp_dir() . '/webshop-visibility-' . bin2hex(random_bytes(8)) . '.json';
try {
    $migration = new WebshopVisibilityMigration($db);
    $result = $migration->apply($backup);
    if ($result['hidden_products'] !== 2 || !$result['removed_product_status'] || count($result['removed_columns']) !== 7) {
        throw new RuntimeException('Migration result differs from the planned legacy fields.');
    }
    $visibility = $db->query('SELECT id,is_hidden FROM webshopproduct ORDER BY id')->fetchAll(PDO::FETCH_KEY_PAIR);
    if ($visibility !== [990001 => 0, 990002 => 1, 990003 => 1, 990004 => 1, 990005 => null]) {
        throw new RuntimeException('The migration did not preserve product visibility.');
    }
    $plan = $migration->inspect();
    if ($plan['legacy_columns'] || $plan['product_status'] !== null) {
        throw new RuntimeException('Second inspection must be empty.');
    }
    $snapshot = json_decode(file_get_contents($backup), true, 512, JSON_THROW_ON_ERROR);
    if (count($snapshot['product_status']['rows']) !== 5 || $snapshot['product_status']['rows'][1]['status'] !== 'draft') {
        throw new RuntimeException('Product visibility backup is incomplete.');
    }
    echo "Visibility migration verified: hidden flags preserved, drafts and archived products hidden, legacy columns removed, rerun empty. Temporary tables only.\n";
} finally { if (file_exists($backup)) { unlink($backup); } }
