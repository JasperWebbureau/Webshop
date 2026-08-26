<?php
use Flexgrid\Form\Input\EmailInput;
use Flexgrid\Form\Input\TextInput;

require_once __DIR__ . '/FormHelper.php';

$checkoutData = is_array($checkoutData ?? null) ? $checkoutData : [];

echo webshopCheckoutRenderSessionForm(
    'contact',
    t('webshop_checkout_contact_title', 'Contactgegevens'),
    'fas fa-user',
    [
        [
            'name' => 'first_name',
            'label' => t('webshop_checkout_first_name', 'Voornaam'),
            'input' => TextInput::class,
            'required' => true,
        ],
        [
            'name' => 'last_name',
            'label' => t('webshop_checkout_last_name', 'Achternaam'),
            'input' => TextInput::class,
            'required' => true,
        ],
        [
            'name' => 'email',
            'label' => t('webshop_checkout_email', 'E-mail'),
            'input' => EmailInput::class,
            'required' => true,
        ],
        [
            'name' => 'phone',
            'label' => t('webshop_checkout_phone', 'Telefoon'),
            'input' => TextInput::class,
        ],
        [
            'name' => 'company_name',
            'label' => t('webshop_checkout_company', 'Bedrijfsnaam'),
            'input' => TextInput::class,
            'wide' => true,
        ],
    ],
    $checkoutData
);
