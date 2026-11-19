<?php

if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../tools/bootstrap.php';

$api = new \Flexgrid\Api\Api();
$api->setUrl('api/WebshopPayment/webhook');
$property = new ReflectionProperty($api, 'endpoint');
$property->setAccessible(true);
$endpoint = $property->getValue($api);
if ($endpoint !== [\Flexgrid\Modules\Webshop\Api\WebshopPayment::class, 'webhook', 'public', ['POST']]) {
    throw new RuntimeException('Module webhook is not registered without an App adapter.');
}
$_SERVER['REQUEST_METHOD'] = 'GET';
$api->execute();
if (http_response_code() !== 405) { throw new RuntimeException('Webhook must reject GET.'); }
echo "Public module webhook registration and POST-only contract verified without provider calls.\n";
