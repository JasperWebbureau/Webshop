<?php

namespace Flexgrid\Modules\Webshop\Service;

use Flexgrid\App\Settings\Settings;
use Flexgrid\Cache\CacheFile;
use Flexgrid\Modules\Webshop\Entity\WebshopOrder;
use Flexgrid\Modules\Webshop\Repository\WebshopOrderLineRepository;
use Flexgrid\Utils\_Mail;

class OrderMailService
{
    public function sendOrderCreatedMails(WebshopOrder $order): void
    {
        if (!$this->isMailEnabled('webshop_send_order_created_mail', 1)) {
            return;
        }

        if ((int)$order->getId() <= 0) {
            return;
        }

        $marker = $this->getMarker($order, 'created');
        if (!empty($marker['sent'])) {
            return;
        }

        new Settings();
        $adminEmail = trim((string)($this->getSettingValue('webshop_admin_email') ?: Settings::Company('email')));
        $customerEmail = trim((string)$order->getCustomerEmail());

        try {
            if (filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
                $this->sendAdminMail($order, $adminEmail);
            }

            if (filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
                $this->sendCustomerMail($order, $customerEmail);
            }

            $this->setMarker($order, 'created', [
                'sent' => true,
                'sent_at' => time(),
                'order_id' => (int)$order->getId(),
            ]);
        } catch (\Throwable $exception) {
            $this->setMarker($order, 'created_error', [
                'sent' => false,
                'error' => $exception->getMessage(),
                'sent_at' => time(),
                'order_id' => (int)$order->getId(),
            ]);
        }
    }

    public function sendStatusMail(WebshopOrder $order, string $statusType): void
    {
        if (!$this->isMailEnabled('webshop_send_status_mail', 1)) {
            return;
        }

        if ((int)$order->getId() <= 0 || !in_array($statusType, ['paid', 'cancelled', 'refunded', 'shipped', 'completed'], true)) {
            return;
        }

        $marker = $this->getMarker($order, 'status_' . $statusType);
        if (!empty($marker['sent'])) {
            return;
        }

        $customerEmail = trim((string)$order->getCustomerEmail());
        if (!filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        try {
            $mail = new _Mail();
            $mail->clearAddresses();
            $mail
                ->setReciever($customerEmail)
                ->setSubject($this->getStatusSubject($order, $statusType))
                ->setHtml($this->buildStatusHtml($order, $statusType))
                ->send();

            $this->setMarker($order, 'status_' . $statusType, [
                'sent' => true,
                'sent_at' => time(),
                'order_id' => (int)$order->getId(),
                'status' => $statusType,
            ]);
        } catch (\Throwable $exception) {
            $this->setMarker($order, 'status_' . $statusType . '_error', [
                'sent' => false,
                'error' => $exception->getMessage(),
                'sent_at' => time(),
                'order_id' => (int)$order->getId(),
                'status' => $statusType,
            ]);
        }
    }

    protected function sendAdminMail(WebshopOrder $order, string $email): void
    {
        $mail = new _Mail();
        $mail->clearAddresses();
        $mail
            ->setReciever($email)
            ->setSubject(t('webshop_mail_admin_order_subject', 'Nieuwe webshopbestelling') . ' ' . $order->getOrderNumber())
            ->setHtml($this->buildAdminHtml($order))
            ->send();
    }

    protected function sendCustomerMail(WebshopOrder $order, string $email): void
    {
        $mail = new _Mail();
        $mail->clearAddresses();
        $mail
            ->setReciever($email)
            ->setSubject(t('webshop_mail_customer_order_subject', 'Bevestiging van je bestelling') . ' ' . $order->getOrderNumber())
            ->setHtml($this->buildCustomerHtml($order))
            ->send();
    }

    protected function buildAdminHtml(WebshopOrder $order): string
    {
        return $this->wrapMail(
            t('webshop_mail_admin_order_title', 'Nieuwe bestelling ontvangen'),
            '<p>' . t('webshop_mail_admin_order_intro', 'Er is een nieuwe bestelling geplaatst in de webshop.') . '</p>' .
            $this->buildSummary($order)
        );
    }

    protected function buildCustomerHtml(WebshopOrder $order): string
    {
        return $this->wrapMail(
            t('webshop_mail_customer_order_title', 'Bedankt voor je bestelling'),
            '<p>' . t('webshop_mail_customer_order_intro', 'We hebben je bestelling ontvangen. Hieronder vind je de samenvatting.') . '</p>' .
            $this->buildSummary($order)
        );
    }

    protected function buildStatusHtml(WebshopOrder $order, string $statusType): string
    {
        $text = [
            'paid' => t('webshop_mail_status_paid_text', 'We hebben je betaling ontvangen. Je bestelling wordt verder verwerkt.'),
            'cancelled' => t('webshop_mail_status_cancelled_text', 'Je bestelling is geannuleerd.'),
            'refunded' => t('webshop_mail_status_refunded_text', 'Je betaling is terugbetaald.'),
            'shipped' => t('webshop_mail_status_shipped_text', 'Je bestelling is verzonden. Hieronder vind je de verzendgegevens.'),
            'completed' => t('webshop_mail_status_completed_text', 'Je bestelling is afgerond.'),
        ];

        return $this->wrapMail(
            $this->getStatusTitle($statusType),
            '<p>' . ($text[$statusType] ?? '') . '</p>' . $this->buildSummary($order)
        );
    }

    protected function getStatusSubject(WebshopOrder $order, string $statusType): string
    {
        return $this->getStatusTitle($statusType) . ' ' . $order->getOrderNumber();
    }

    protected function getStatusTitle(string $statusType): string
    {
        $titles = [
            'paid' => t('webshop_mail_status_paid_title', 'Betaling ontvangen'),
            'cancelled' => t('webshop_mail_status_cancelled_title', 'Bestelling geannuleerd'),
            'refunded' => t('webshop_mail_status_refunded_title', 'Betaling terugbetaald'),
            'shipped' => t('webshop_mail_status_shipped_title', 'Bestelling verzonden'),
            'completed' => t('webshop_mail_status_completed_title', 'Bestelling afgerond'),
        ];

        return $titles[$statusType] ?? t('webshop_mail_status_update_title', 'Update over je bestelling');
    }

    protected function buildSummary(WebshopOrder $order): string
    {
        $rows = [
            t('webshop_mail_order_number', 'Bestelnummer') => $order->getOrderNumber(),
            t('webshop_mail_customer_name', 'Naam') => $order->getCustomerName(),
            t('webshop_mail_customer_email', 'E-mail') => $order->getCustomerEmail(),
            t('webshop_mail_payment_status', 'Betaalstatus') => $order->getPaymentStatus(),
            t('webshop_mail_order_status', 'Orderstatus') => $order->getStatus(),
            t('webshop_mail_tracking_code', 'Track & trace code') => $order->getShippingTrackingCode(),
            t('webshop_mail_tracking_url', 'Track & trace link') => $order->getShippingTrackingUrl(),
            t('webshop_mail_subtotal', 'Subtotaal') => $this->formatMoney((float)$order->getSubtotal()),
            t('webshop_mail_shipping', 'Verzending') => $this->formatMoney((float)$order->getShippingTotal()),
            t('webshop_mail_discount', 'Korting') => $order->getDiscountTotal() > 0 ? '-' . $this->formatMoney((float)$order->getDiscountTotal()) : '',
            t('webshop_mail_tax_total', 'BTW') => $this->formatMoney((float)$order->getTaxTotal()),
            t('webshop_mail_total', 'Totaal') => $this->formatMoney((float)$order->getGrandTotal()),
        ];

        $html = '<table cellpadding="6" cellspacing="0" border="0">';
        foreach ($rows as $label => $value) {
            if ((string)$value === '') {
                continue;
            }

            $html .= '<tr><th align="left">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</th><td>' . htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8') . '</td></tr>';
        }
        $html .= '</table>';

        $html .= '<h3>' . htmlspecialchars(t('webshop_mail_order_lines', 'Bestelde producten'), ENT_QUOTES, 'UTF-8') . '</h3>';
        $html .= '<table cellpadding="6" cellspacing="0" border="0">';
        foreach ((new WebshopOrderLineRepository())->getByOrderId((int)$order->getId()) as $line) {
            $title = (string)$line->getProductTitle();

            $html .= '<tr><td>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . ' x ' . (int)$line->getQuantity() . '</td><td align="right">' . htmlspecialchars($this->formatMoney((float)$line->getLineTotal()), ENT_QUOTES, 'UTF-8') . '</td></tr>';
        }

        return $html . '</table>';
    }

    protected function wrapMail(string $title, string $body): string
    {
        return '<h2>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h2>' . $body;
    }

    protected function formatMoney(float $amount): string
    {
        return 'EUR ' . number_format($amount, 2, ',', '.');
    }

    protected function getSettingValue(string $key): string
    {
        $setting = Settings::get($key, [
            'value' => '',
            'label' => 'Webshop admin e-mail',
        ]);

        return trim((string)($setting['value'] ?? ''));
    }

    protected function isMailEnabled(string $key, int $default): bool
    {
        new Settings();
        $setting = Settings::get($key, [
            'value' => $default,
            'label' => $key,
        ]);

        return (int)($setting['value'] ?? $default) === 1;
    }

    protected function getMarker(WebshopOrder $order, string $type): array
    {
        $content = $this->getMarkerFile($order, $type)->getContent();

        return is_array($content) ? $content : [];
    }

    protected function setMarker(WebshopOrder $order, string $type, array $data): void
    {
        $this->getMarkerFile($order, $type)->setContent($data);
    }

    protected function getMarkerFile(WebshopOrder $order, string $type): CacheFile
    {
        return new CacheFile('/WebshopMail/' . md5($type . ':order:' . $order->getId()) . '.json', true);
    }
}
