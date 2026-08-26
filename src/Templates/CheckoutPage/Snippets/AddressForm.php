<?php
use Flexgrid\Form\Input\TextInput;

require_once __DIR__ . '/FormHelper.php';

$checkoutData = is_array($checkoutData ?? null) ? $checkoutData : [];

echo webshopCheckoutRenderSessionForm(
    'address',
    t('webshop_checkout_address_title', 'Adres'),
    'fas fa-map-marker-alt',
    [
        [
            'name' => 'address',
            'label' => t('webshop_checkout_address', 'Adres'),
            'input' => TextInput::class,
            'required' => true,
            'wide' => true,
        ],
        [
            'name' => 'postal_code',
            'label' => t('webshop_checkout_postal_code', 'Postcode'),
            'input' => TextInput::class,
            'required' => true,
        ],
        [
            'name' => 'city',
            'label' => t('webshop_checkout_city', 'Plaats'),
            'input' => TextInput::class,
            'required' => true,
        ],
    ],
    $checkoutData
);
