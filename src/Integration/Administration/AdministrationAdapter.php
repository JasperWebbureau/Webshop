<?php

namespace Flexgrid\Modules\Webshop\Integration\Administration;

use Flexgrid\Modules\AdminCore\ValueObject\Currency;
use Flexgrid\Modules\AdminCore\ValueObject\Money;
use Flexgrid\Modules\AdminCustomer\Application\Command\CreateCustomerCommand;
use Flexgrid\Modules\AdminCustomer\Service\AdminCustomerFactory;
use Flexgrid\Modules\AdminInvoice\Application\Command\CreateInvoiceCommand;
use Flexgrid\Modules\AdminInvoice\Service\AdminInvoiceFactory;
use Flexgrid\Modules\AdminInvoice\Service\InvoiceCalculator;
use Flexgrid\Modules\AdminPayment\Application\Command\RegisterInvoicePaymentCommand;
use Flexgrid\Modules\AdminPayment\Integration\Invoice\InvoicePaymentFactory;

/** Only this integration directory knows concrete Admin modules. Caller owns transaction/row lock. */
final class AdministrationAdapter
{
    public function post($order, array $snapshot, array $job): array
    {
        $mapped = $job['pricing'];
        $name = trim((string)($snapshot['name'] ?? ''));
        $company = trim((string)($snapshot['company_name'] ?? ''));
        $party = ['name' => $company ?: $name, 'contact_name' => $name,
            'email' => (string)($snapshot['email'] ?? ''), 'phone' => (string)($snapshot['phone'] ?? '')];
        $address = ['line_1' => (string)($snapshot['address'] ?? ''), 'postal_code' => (string)($snapshot['postal_code'] ?? ''),
            'city' => (string)($snapshot['city'] ?? ''), 'country_code' => (string)($snapshot['country'] ?? 'NL')];
        $customer = AdminCustomerFactory::createCreateCustomer()->execute(new CreateCustomerCommand(
            $party['name'], $company, '', '',
            [['name' => $name, 'email' => $party['email'], 'phone' => $party['phone'], 'is_primary' => true]],
            [array_merge($address, ['type' => 'billing', 'is_primary' => true])],
            '', 'webshop', $job['shop_id'] . ':customer:' . $order->getCustomerId()
        ));
        $party['source_public_id'] = $customer->getPublicId();
        $invoice = AdminInvoiceFactory::createCreateInvoice()->execute(new CreateInvoiceCommand(
            (string)$order->getCurrency(), $job['issue_date'], $job['issue_date'], $party, $address,
            $mapped['lines'], (string)$order->getOrderNumber(), !empty($job['test']) ? 'TEST webshopbetaling' : '', 'webshop', $job['shop_id'] . ':order:' . $order->getId()
        ));
        $totals = (new InvoiceCalculator())->calculate($invoice);
        if ($invoice->getCustomerSnapshot()->getSourcePublicId() !== $customer->getPublicId()
            || $invoice->getCustomerReference() !== (string)$order->getOrderNumber()
            || $invoice->getIssueDate()->getValue() !== $job['issue_date']
            || $invoice->getCurrency()->getCode() !== (string)$order->getCurrency()
            || $totals->getNet()->getMinorUnits() !== $mapped['net']
            || $totals->getTax()->getMinorUnits() !== $mapped['tax']
            || $totals->getGross()->getMinorUnits() !== $mapped['gross']) {
            throw new \DomainException('AdminInvoice wijkt af van de bevroren order.');
        }
        if ($invoice->getStatus()->getValue() === 'draft') {
            AdminInvoiceFactory::createFinalizeInvoice()->execute($invoice->getPublicId());
        }
        InvoicePaymentFactory::createRegisterInvoicePayment()->execute(new RegisterInvoicePaymentCommand(
            $invoice->getPublicId(), (new Money($mapped['gross'], new Currency((string)$order->getCurrency())))->format('.', ''),
            (string)$order->getCurrency(), $job['booked_on'], 'receipt', 'Webshop ' . $order->getOrderNumber(),
            $job['payment_reference'], 'webshop', $job['shop_id'] . ':payment:' . $job['payment_reference']
        ));
        return ['customer_public_id' => $customer->getPublicId(), 'invoice_public_id' => $invoice->getPublicId(),
            'invoice_number' => AdminInvoiceFactory::createGetInvoice()->execute($invoice->getPublicId())->getInvoiceNumber()];
    }
}
