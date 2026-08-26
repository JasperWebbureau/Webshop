<?php
use Flexgrid\Form\Input\TextAreaInput;
use Flexgrid\Form\Input\TextInput;

require_once __DIR__ . '/FormHelper.php';

$checkoutData = is_array($checkoutData ?? null) ? $checkoutData : [];

echo webshopCheckoutRenderSessionForm(
    'extra',
    t('webshop_checkout_extra_title', 'Extra'),
    'fas fa-pen',
    [
        [
            'name' => 'note',
            'label' => t('webshop_checkout_note', 'Opmerking'),
            'input' => TextAreaInput::class,
            'rows' => 4,
            'wide' => true,
        ],
        [
            'name' => 'discount_code',
            'label' => t('webshop_checkout_discount_code', 'Kortingscode'),
            'input' => TextInput::class,
            'wide' => true,
        ],
    ],
    $checkoutData
);
