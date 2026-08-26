<?php

namespace Flexgrid\Modules\Webshop\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Flexgrid\App\Settings\Settings;
use Flexgrid\Modules\Webshop\Entity\WebshopInvoice;
use Flexgrid\Modules\Webshop\Repository\WebshopInvoiceLineRepository;
use Flexgrid\Modules\Webshop\Repository\WebshopInvoiceRepository;

class InvoicePdfService
{
    public function generate(WebshopInvoice $invoice): array
    {
        if ((int)$invoice->getId() <= 0) {
            return ['success' => false, 'message' => 'Factuur niet gevonden.'];
        }

        $this->ensureDompdfLoaded();
        if (!class_exists(Dompdf::class)) {
            return ['success' => false, 'message' => 'Dompdf is niet beschikbaar.'];
        }

        $relativePath = $this->getRelativePath($invoice);
        $absolutePath = $this->getAbsolutePath($relativePath);
        $this->ensureDirectory(dirname($absolutePath));

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($this->buildHtml($invoice));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        file_put_contents($absolutePath, $dompdf->output());

        $invoice->setPdfFile($relativePath);
        $invoice = (new WebshopInvoiceRepository())->add($invoice);

        return [
            'success' => true,
            'message' => 'Factuur PDF aangemaakt.',
            'invoice' => $invoice,
            'path' => $relativePath,
        ];
    }

    protected function buildHtml(WebshopInvoice $invoice): string
    {
        new Settings();
        $snapshot = json_decode((string)$invoice->getCustomerSnapshot(), true);
        if (!is_array($snapshot)) {
            $snapshot = [];
        }

        $lines = (new WebshopInvoiceLineRepository())->getByInvoiceId((int)$invoice->getId());
        $companyName = (string)(Settings::Company('name') ?: Settings::get('company_name')['value'] ?? '');
        $companyEmail = (string)(Settings::Company('email') ?: Settings::get('company_email')['value'] ?? '');
        $companyVat = (string)(Settings::get('company_vat_nr')['value'] ?? '');

        $html = '<!doctype html><html><head><meta charset="utf-8"><style>' . $this->getCss() . '</style></head><body>';
        $html .= '<header class="invoice-header">';
        $html .= '<div><h1>Factuur</h1><strong>' . $this->escape($invoice->getInvoiceNumber()) . '</strong></div>';
        $html .= '<div class="company"><strong>' . $this->escape($companyName) . '</strong><br>' . $this->escape($companyEmail);
        if ($companyVat !== '') {
            $html .= '<br>BTW: ' . $this->escape($companyVat);
        }
        $html .= '</div></header>';

        $html .= '<section class="meta">';
        $html .= '<div><h2>Factuurgegevens</h2><p>Datum: ' . date('d-m-Y', (int)$invoice->getInvoiceDate()) . '<br>Order: #' . (int)$invoice->getOrderId() . '<br>Status: ' . $this->escape($invoice->getStatus()) . '</p></div>';
        $html .= '<div><h2>Factuur aan</h2><p><strong>' . $this->escape($invoice->getCustomerName()) . '</strong><br>' . $this->escape($invoice->getCustomerEmail()) . '<br>' . $this->escape((string)($snapshot['address'] ?? '')) . '<br>' . $this->escape((string)($snapshot['postal_code'] ?? '')) . ' ' . $this->escape((string)($snapshot['city'] ?? '')) . '</p></div>';
        $html .= '</section>';

        $html .= '<table><thead><tr><th>Omschrijving</th><th>SKU</th><th class="right">Aantal</th><th class="right">Prijs</th><th class="right">BTW</th><th class="right">Totaal</th></tr></thead><tbody>';
        foreach ($lines as $line) {
            $html .= '<tr>';
            $html .= '<td>' . $this->escape($line->getTitle()) . '</td>';
            $html .= '<td>' . $this->escape($line->getSku()) . '</td>';
            $html .= '<td class="right">' . (int)$line->getQuantity() . '</td>';
            $html .= '<td class="right">' . $this->formatMoney((float)$line->getUnitPrice()) . '</td>';
            $html .= '<td class="right">' . $this->formatMoney((float)$line->getTaxTotal()) . '</td>';
            $html .= '<td class="right">' . $this->formatMoney((float)$line->getLineTotal()) . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';

        $html .= '<section class="totals">';
        $html .= '<div><span>Subtotaal</span><strong>' . $this->formatMoney((float)$invoice->getSubtotal()) . '</strong></div>';
        $html .= '<div><span>BTW</span><strong>' . $this->formatMoney((float)$invoice->getTaxTotal()) . '</strong></div>';
        $html .= '<div class="grand"><span>Totaal</span><strong>' . $this->formatMoney((float)$invoice->getGrandTotal()) . '</strong></div>';
        $html .= '</section>';

        return $html . '</body></html>';
    }

    protected function getCss(): string
    {
        return '
            body { color: #1f2933; font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; line-height: 1.45; margin: 32px; }
            h1 { font-size: 30px; margin: 0 0 6px; }
            h2 { font-size: 14px; margin: 0 0 8px; }
            .invoice-header { border-bottom: 2px solid #1f2933; display: table; margin-bottom: 28px; padding-bottom: 18px; width: 100%; }
            .invoice-header > div { display: table-cell; vertical-align: top; width: 50%; }
            .company { text-align: right; }
            .meta { display: table; margin-bottom: 26px; width: 100%; }
            .meta > div { display: table-cell; vertical-align: top; width: 50%; }
            table { border-collapse: collapse; margin-bottom: 22px; width: 100%; }
            th { background: #f3f4f6; border-bottom: 1px solid #d7dce1; padding: 8px; text-align: left; }
            td { border-bottom: 1px solid #e5e7eb; padding: 8px; vertical-align: top; }
            .right { text-align: right; }
            .totals { margin-left: auto; width: 260px; }
            .totals div { display: table; padding: 5px 0; width: 100%; }
            .totals span, .totals strong { display: table-cell; }
            .totals strong { text-align: right; }
            .totals .grand { border-top: 2px solid #1f2933; font-size: 15px; margin-top: 6px; padding-top: 8px; }
        ';
    }

    protected function getRelativePath(WebshopInvoice $invoice): string
    {
        $fileName = preg_replace('/[^a-zA-Z0-9\-_]/', '-', (string)$invoice->getInvoiceNumber());

        return 'Files/storage/Webshop/Invoices/' . $fileName . '.pdf';
    }

    protected function getAbsolutePath(string $relativePath): string
    {
        return rtrim((string)__ROOTDIR__, '/\\') . '/' . trim($relativePath, '/\\');
    }

    protected function ensureDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }
    }

    protected function ensureDompdfLoaded(): void
    {
        if (!class_exists(Dompdf::class) && is_file('vendor/autoload.php')) {
            require_once 'vendor/autoload.php';
        }
    }

    protected function escape($value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    protected function formatMoney(float $amount): string
    {
        return 'EUR ' . number_format($amount, 2, ',', '.');
    }
}
