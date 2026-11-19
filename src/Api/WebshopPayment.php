<?php

namespace Flexgrid\Modules\Webshop\Api;

use Flexgrid\Modules\Webshop\Service\MolliePaymentService;

final class WebshopPayment
{
    public function webhook(array $parameters = []): void
    {
        $id = $_POST['id'] ?? '';
        $result = is_string($id) ? (new MolliePaymentService())->syncPayment($id) : ['success' => false];
        http_response_code(!empty($result['success']) ? 200 : 503);
        header('Content-Type: text/plain; charset=utf-8');
        echo !empty($result['success']) ? 'ok' : 'retry';
    }
}
