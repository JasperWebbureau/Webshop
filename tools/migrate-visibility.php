<?php

if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }

/** One-time migration; never runs during normal webshop requests or force scans. */
final class WebshopVisibilityMigration
{
    private const TABLES = [
        'webshopproduct', 'webshopproductgroup', 'webshopproductmaingroup',
        'webshopshippingmethod', 'webshoptaxrate', 'webshopdiscountcode', 'webshopmoodboard',
    ];

    private $db;

    public function __construct(PDO $db) { $this->db = $db; }

    public function inspect(): array
    {
        $tables = [];
        $productStatus = null;
        foreach (self::TABLES as $table) {
            $exists = $this->db->query("SHOW TABLES LIKE '" . $table . "'")->fetchColumn();
            if (!$exists) { continue; }
            if ($table === 'webshopproduct' && $this->db->query("SHOW COLUMNS FROM webshopproduct WHERE Field = 'status'")->fetchColumn()) {
                $productStatus = [
                    'schema' => $this->db->query('SHOW CREATE TABLE webshopproduct')->fetch(PDO::FETCH_NUM)[1],
                    'rows' => $this->db->query('SELECT id, is_hidden, status FROM webshopproduct ORDER BY id')->fetchAll(PDO::FETCH_ASSOC),
                ];
            }
            $column = $this->db->query('SHOW COLUMNS FROM `' . $table . '` LIKE \'is_active\'')->fetch(PDO::FETCH_ASSOC);
            if ($column) {
                $tables[$table] = [
                    'schema' => $this->db->query('SHOW CREATE TABLE `' . $table . '`')->fetch(PDO::FETCH_NUM)[1],
                    'rows' => $this->db->query('SELECT id, is_hidden, is_active FROM `' . $table . '` ORDER BY id')->fetchAll(PDO::FETCH_ASSOC),
                ];
            }
        }
        return ['legacy_columns' => $tables, 'product_status' => $productStatus];
    }

    public function apply(string $backupPath): array
    {
        if ($this->db->inTransaction()) { throw new LogicException('Schemawijzigingen vereisen een eigen migratie buiten een transactie.'); }
        if ($backupPath === '' || file_exists($backupPath) || !is_writable(dirname($backupPath))) {
            throw new InvalidArgumentException('Kies een nieuw backupbestand in een bestaande schrijfbare map.');
        }
        $snapshot = $this->inspect();
        $json = json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        if (file_put_contents($backupPath, $json, LOCK_EX) !== strlen($json)) {
            throw new RuntimeException('Backup kon niet volledig worden opgeslagen. Er is niets gemigreerd.');
        }
        $hiddenProducts = 0;
        if ($snapshot['product_status'] !== null) {
            $this->db->beginTransaction();
            try {
                $hiddenProducts = $this->db->exec("UPDATE webshopproduct SET is_hidden = 1
                    WHERE (is_hidden IS NULL OR is_hidden = 0)
                      AND status IS NOT NULL AND TRIM(status) <> '' AND status <> 'published'");
                $this->db->commit();
            } catch (Throwable $error) {
                $this->db->rollBack();
                throw $error;
            }
        }
        // MySQL DDL commits implicitly. The backup is durable before the first DROP.
        foreach (array_keys($snapshot['legacy_columns']) as $table) {
            $this->db->exec('ALTER TABLE `' . $table . '` DROP COLUMN `is_active`');
        }
        if ($snapshot['product_status'] !== null) {
            $this->db->exec('ALTER TABLE webshopproduct DROP COLUMN status');
        }
        return [
            'removed_columns' => array_keys($snapshot['legacy_columns']),
            'removed_product_status' => $snapshot['product_status'] !== null,
            'hidden_products' => $hiddenProducts,
        ];
    }
}

if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    require __DIR__ . '/bootstrap.php';
    $migration = new WebshopVisibilityMigration(\Flexgrid\Database\Connection::getConnections());
    $options = getopt('', ['apply', 'backup:']);
    if (isset($options['apply'])) {
        echo json_encode($migration->apply((string)($options['backup'] ?? '')), JSON_PRETTY_PRINT) . PHP_EOL;
    } else {
        $plan = $migration->inspect();
        echo json_encode(['legacy_columns' => array_keys($plan['legacy_columns']), 'has_product_status' => $plan['product_status'] !== null], JSON_PRETTY_PRINT) . PHP_EOL;
    }
}
