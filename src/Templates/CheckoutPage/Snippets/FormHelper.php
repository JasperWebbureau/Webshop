<?php

use Flexgrid\Event\AjaxEvent;
use Flexgrid\Form\Form;
use Flexgrid\Form\Input\Input;
use Flexgrid\Form\Input\TextInput;
use Flexgrid\Modules\Webshop\Controller\WebshopController;

if (!function_exists('webshopCheckoutCreateInput')) {
    function webshopCheckoutCreateInput(array $field, array $checkoutData): Input
    {
        $name = (string)($field['name'] ?? '');
        $inputClass = $field['input'] ?? TextInput::class;

        /** @var Input $input */
        $input = new $inputClass($name, 'checkout');
        $input->setIsFrontend(true);
        $input->setLabel((string)($field['label'] ?? $name));
        $input->setValue((string)($checkoutData[$name] ?? ''));
        $input->getWrapperElement()->addClass('webshop-checkout-page__field');

        if (!empty($field['wide'])) {
            $input->getWrapperElement()->addClass('webshop-checkout-page__wide');
        }

        if (!empty($field['required'])) {
            $input->getInputElement()->setAttribute('required', 'required');
        }

        if (!empty($field['rows'])) {
            $input->getInputElement()->setAttribute('rows', (int)$field['rows']);
        }

        return $input;
    }
}

if (!function_exists('webshopCheckoutRenderSessionForm')) {
    function webshopCheckoutRenderSessionForm(string $step, string $title, string $icon, array $fields, array $checkoutData): string
    {
        $event = new AjaxEvent(WebshopController::class, 'saveCheckoutSession');
        $event->setMinimumAccessLevel(0);

        $form = new Form('checkout');
        $form->setMethod('ajax');
        $form->addEvent($event);
        $form->set('class', 'webshop-checkout-page__step-form webshop-checkout-page__fields');
        $form->set('data-webshop-checkout-session-form', 'true');
        $form->set('data-webshop-checkout-step', $step);

        foreach ($fields as $field) {
            $form->addInput(webshopCheckoutCreateInput($field, $checkoutData));
        }

        return '<section class="webshop-checkout-page__form-section">' .
            '<div class="webshop-checkout-page__section-title">' .
            '<span class="webshop-checkout-page__section-icon"><i class="' . htmlspecialchars($icon, ENT_QUOTES, 'UTF-8') . '" aria-hidden="true"></i></span>' .
            '<h2>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h2>' .
            '</div>' .
            (string)$form .
            '</section>';
    }
}
