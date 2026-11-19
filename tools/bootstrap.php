<?php

if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
chdir(dirname(__DIR__, 4));
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['populate_from_request'] = false;
require_once 'Files/Boot/.env.php';
require_once 'vendor/autoload.php';
require_once 'Files/Boot/Functions.php';
require_once 'Flexgrid/Flexgrid/src/Utils/_File.php';
require_once 'Flexgrid/Flexgrid/src/Utils/_Debugger.php';
require_once 'Flexgrid/Flexgrid/src/Utils/_Dir.php';
require_once 'Flexgrid/Flexgrid/src/Cache/CacheFile.php';
require_once 'Flexgrid/Flexgrid/src/Autoloader.php';
spl_autoload_register('Autoloader::autoload');
if (!defined('__AJAX__')) { define('__AJAX__', false); }
if (!defined('__FLEXGRID_ACCESS__')) { define('__FLEXGRID_ACCESS__', false); }
\Flexgrid\Database\Connection::addDatabase($db['name'], $db['host'], $db['username'], $db['password']);
$webshopDatabaseIsLocal = in_array(strtolower($db['host']), ['localhost', '127.0.0.1', '::1'], true);
unset($db);
