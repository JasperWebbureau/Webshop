<?php

namespace Flexgrid\Modules\Webshop\Service;

use Flexgrid\Modules\Webshop\Repository\WebshopInvoiceRepository;
use Flexgrid\Modules\Webshop\Repository\WebshopOrderRepository;

class ReportService
{
    public function getOverview(): array
    {
        $orders = (new WebshopOrderRepository())->getRecent(1000);
        $invoices = (new WebshopInvoiceRepository())->getRecent(1000);

        return [
            'orderSummary' => $this->summarizeOrders($orders),
            'invoiceSummary' => $this->summarizeInvoices($invoices),
            'monthly' => $this->getMonthlyRows($orders, $invoices),
            'yearly' => $this->getYearlyRows($orders, $invoices),
        ];
    }

    protected function summarizeOrders(array $orders): array
    {
        $summary = [
            'count' => count($orders),
            'paid_count' => 0,
            'revenue' => 0.0,
            'tax' => 0.0,
        ];

        foreach ($orders as $order) {
            if ((string)$order->getPaymentStatus() !== 'paid') {
                continue;
            }

            $summary['paid_count']++;
            $summary['revenue'] += (float)$order->getGrandTotal();
            $summary['tax'] += (float)$order->getTaxTotal();
        }

        return $this->formatSummary($summary);
    }

    protected function summarizeInvoices(array $invoices): array
    {
        $summary = [
            'count' => count($invoices),
            'paid_count' => 0,
            'revenue' => 0.0,
            'tax' => 0.0,
        ];

        foreach ($invoices as $invoice) {
            if ((string)$invoice->getStatus() === 'cancelled') {
                continue;
            }

            $summary['paid_count']++;
            $summary['revenue'] += (float)$invoice->getGrandTotal();
            $summary['tax'] += (float)$invoice->getTaxTotal();
        }

        return $this->formatSummary($summary);
    }

    protected function getMonthlyRows(array $orders, array $invoices): array
    {
        $rows = [];

        foreach ($orders as $order) {
            if ((string)$order->getPaymentStatus() !== 'paid') {
                continue;
            }

            $key = date('Y-m', (int)$order->getMakeTime());
            $this->ensureReportRow($rows, $key, date('m-Y', (int)$order->getMakeTime()));
            $rows[$key]['orders']++;
            $rows[$key]['order_revenue'] += (float)$order->getGrandTotal();
            $rows[$key]['order_tax'] += (float)$order->getTaxTotal();
        }

        foreach ($invoices as $invoice) {
            if ((string)$invoice->getStatus() === 'cancelled') {
                continue;
            }

            $key = date('Y-m', (int)$invoice->getInvoiceDate());
            $this->ensureReportRow($rows, $key, date('m-Y', (int)$invoice->getInvoiceDate()));
            $rows[$key]['invoices']++;
            $rows[$key]['invoice_revenue'] += (float)$invoice->getGrandTotal();
            $rows[$key]['invoice_tax'] += (float)$invoice->getTaxTotal();
        }

        krsort($rows);
        return $this->formatRows($rows);
    }

    protected function getYearlyRows(array $orders, array $invoices): array
    {
        $rows = [];

        foreach ($orders as $order) {
            if ((string)$order->getPaymentStatus() !== 'paid') {
                continue;
            }

            $key = date('Y', (int)$order->getMakeTime());
            $this->ensureReportRow($rows, $key, $key);
            $rows[$key]['orders']++;
            $rows[$key]['order_revenue'] += (float)$order->getGrandTotal();
            $rows[$key]['order_tax'] += (float)$order->getTaxTotal();
        }

        foreach ($invoices as $invoice) {
            if ((string)$invoice->getStatus() === 'cancelled') {
                continue;
            }

            $key = date('Y', (int)$invoice->getInvoiceDate());
            $this->ensureReportRow($rows, $key, $key);
            $rows[$key]['invoices']++;
            $rows[$key]['invoice_revenue'] += (float)$invoice->getGrandTotal();
            $rows[$key]['invoice_tax'] += (float)$invoice->getTaxTotal();
        }

        krsort($rows);
        return $this->formatRows($rows);
    }

    protected function ensureReportRow(array &$rows, string $key, string $label): void
    {
        if (isset($rows[$key])) {
            return;
        }

        $rows[$key] = [
            'label' => $label,
            'orders' => 0,
            'invoices' => 0,
            'order_revenue' => 0.0,
            'invoice_revenue' => 0.0,
            'order_tax' => 0.0,
            'invoice_tax' => 0.0,
        ];
    }

    protected function formatRows(array $rows): array
    {
        foreach ($rows as $key => $row) {
            $rows[$key]['order_revenue_formatted'] = $this->formatMoney((float)$row['order_revenue']);
            $rows[$key]['invoice_revenue_formatted'] = $this->formatMoney((float)$row['invoice_revenue']);
            $rows[$key]['order_tax_formatted'] = $this->formatMoney((float)$row['order_tax']);
            $rows[$key]['invoice_tax_formatted'] = $this->formatMoney((float)$row['invoice_tax']);
        }

        return array_values($rows);
    }

    protected function formatSummary(array $summary): array
    {
        $summary['revenue_formatted'] = $this->formatMoney((float)$summary['revenue']);
        $summary['tax_formatted'] = $this->formatMoney((float)$summary['tax']);

        return $summary;
    }

    protected function formatMoney(float $value): string
    {
        return '&euro; ' . number_format($value, 2, ',', '.');
    }
}
