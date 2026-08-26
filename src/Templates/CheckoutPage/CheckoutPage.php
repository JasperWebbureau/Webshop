<?php
use Flexgrid\Event\AjaxEvent;
use Flexgrid\Modules\Webshop\Controller\WebshopController;
use Flexgrid\Response\PageResponse;
use Flexgrid\Response\TemplateResponse;

PageResponse::addAsset('Flexgrid/Modules/Webshop/src/Templates/CheckoutPage/Js/CheckoutPage.js');

$checkoutData = is_array($checkoutData ?? null) ? $checkoutData : [];
$checkoutEvent = new AjaxEvent(WebshopController::class, 'submitCheckout');
$checkoutEvent->setMinimumAccessLevel(0);
$saveEvent = new AjaxEvent(WebshopController::class, 'saveCheckoutSession');
$saveEvent->setMinimumAccessLevel(0);
$paymentDescriptions = [
    'manual' => t('webshop_payment_method_manual_description', 'Betaal achteraf of volgens afspraak. Je bestelling wordt direct geplaatst.'),
    'mollie' => t('webshop_payment_method_mollie_description', 'Je wordt veilig doorgestuurd naar Mollie. Daar kies je onder andere iDEAL of Wero.'),
];
$paymentIcons = [
    'manual' => 'fas fa-file-invoice',
    'mollie' => 'fas fa-credit-card',
];
$subtotal = (float)($summary['subtotal'] ?? 0);
$selectedShippingMethodId = (int)($checkoutData['shipping_method_id'] ?? 0);
$selectedShippingMethod = !empty($shippingMethods) ? $shippingMethods[0] : null;

if ($selectedShippingMethodId > 0 && !empty($shippingMethods)) {
    foreach ($shippingMethods as $shippingMethod) {
        if ((int)$shippingMethod['method']->getId() === $selectedShippingMethodId) {
            $selectedShippingMethod = $shippingMethod;
            break;
        }
    }
}

$initialShippingPrice = $selectedShippingMethod ? (float)($selectedShippingMethod['price'] ?? 0) : 0.0;
$selectedPaymentProvider = (string)($checkoutData['payment_provider'] ?? $defaultPaymentProvider);

if (!empty($paymentMethods) && !array_key_exists($selectedPaymentProvider, $paymentMethods)) {
    $paymentKeys = array_keys($paymentMethods);
    $selectedPaymentProvider = (string)reset($paymentKeys);
}
?>
<section
    class="webshop-checkout-page"
    data-webshop-checkout
    data-subtotal="<?=htmlspecialchars(number_format($subtotal, 2, '.', ''), ENT_QUOTES, 'UTF-8')?>"
    data-save-action="<?=htmlspecialchars($saveEvent->getName(), ENT_QUOTES, 'UTF-8')?>"
    data-discount-empty="<?=htmlspecialchars(t('webshop_checkout_discount_empty_hint', 'Geen kortingscode ingevuld.'), ENT_QUOTES, 'UTF-8')?>"
    data-discount-filled="<?=htmlspecialchars(t('webshop_checkout_discount_filled_hint', 'Kortingscode "{code}" wordt gecontroleerd bij het plaatsen van de bestelling.'), ENT_QUOTES, 'UTF-8')?>"
>
    <grid>
        <div style="--cw:12;--cw-sm:12;--cw-xs:12">
            <div class="webshop-checkout-page__header">
                <div>
                    <p><?=t('webshop_checkout_eyebrow', 'Webshop')?></p>
                    <h1><?=t('webshop_checkout_title', 'Afrekenen')?></h1>
                </div>
                <?php if (!empty($items)) { ?>
                    <div class="webshop-checkout-page__secure">
                        <i class="fas fa-lock" aria-hidden="true"></i>
                        <span><?=t('webshop_checkout_secure', 'Veilig afrekenen')?></span>
                    </div>
                <?php } ?>
            </div>
        </div>

        <?php if (empty($items)) { ?>
            <div class="webshop-checkout-page__empty" style="--cw:12;--cw-sm:12;--cw-xs:12">
                <i class="fas fa-shopping-cart"></i>
                <h2><?=t('webshop_checkout_empty_title', 'Je winkelwagen is leeg')?></h2>
                <p><?=t('webshop_checkout_empty', 'Je winkelwagen is leeg.')?></p>
            </div>
        <?php } else { ?>
            <div class="webshop-checkout-page__steps" style="--cw:12;--cw-sm:12;--cw-xs:12">
                <span class="webshop-checkout-page__step webshop-checkout-page__step--active">
                    <i class="fas fa-user" aria-hidden="true"></i>
                    <?=t('webshop_checkout_step_details', 'Gegevens')?>
                </span>
                <span class="webshop-checkout-page__step">
                    <i class="fas fa-truck" aria-hidden="true"></i>
                    <?=t('webshop_checkout_step_shipping', 'Verzending')?>
                </span>
                <span class="webshop-checkout-page__step">
                    <i class="fas fa-credit-card" aria-hidden="true"></i>
                    <?=t('webshop_checkout_step_payment', 'Betaling')?>
                </span>
            </div>
            <div class="webshop-checkout-page__form" style="--cw:8;--cw-sm:12;--cw-xs:12">
                <?=new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/CheckoutPage/Snippets/ContactForm.php', [
                    'checkoutData' => $checkoutData,
                ])?>
                <?=new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/CheckoutPage/Snippets/AddressForm.php', [
                    'checkoutData' => $checkoutData,
                ])?>
                <?=new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/CheckoutPage/Snippets/ExtraForm.php', [
                    'checkoutData' => $checkoutData,
                ])?>

                <div class="webshop-checkout-page__form-save-feedback" data-webshop-checkout-save-feedback></div>

                <form class="webshop-checkout-page__checkout-form" ajax="true" action="<?=$checkoutEvent->getName()?>" data-webshop-checkout-submit-form>
                    <?php if (!empty($shippingMethods)) { ?>
                        <section class="webshop-checkout-page__form-section webshop-checkout-page__shipping">
                            <div class="webshop-checkout-page__section-title">
                                <span class="webshop-checkout-page__section-icon"><i class="fas fa-truck" aria-hidden="true"></i></span>
                                <h2><?=t('webshop_checkout_shipping_method', 'Verzendmethode')?></h2>
                            </div>
                            <?php foreach ($shippingMethods as $index => $shippingMethod) {
                                $method = $shippingMethod['method'];
                                $isChecked = $selectedShippingMethodId > 0 ? (int)$method->getId() === $selectedShippingMethodId : $index === 0;
                                ?>
                                <label class="webshop-checkout-page__shipping-option">
                                    <input
                                        type="radio"
                                        name="checkout[shipping_method_id]"
                                        value="<?=(int)$method->getId()?>"
                                        data-title="<?=htmlspecialchars((string)$method->getTitle(), ENT_QUOTES, 'UTF-8')?>"
                                        data-price="<?=htmlspecialchars(number_format((float)$shippingMethod['price'], 2, '.', ''), ENT_QUOTES, 'UTF-8')?>"
                                        data-price-label="<?=htmlspecialchars((string)$shippingMethod['price_formatted'], ENT_QUOTES, 'UTF-8')?>"
                                        <?=$isChecked ? 'checked' : ''?>
                                    >
                                    <span>
                                        <?=htmlspecialchars((string)$method->getTitle(), ENT_QUOTES, 'UTF-8')?>
                                        <?php if (trim((string)$method->getDescription()) !== '') { ?>
                                            <small><?=htmlspecialchars((string)$method->getDescription(), ENT_QUOTES, 'UTF-8')?></small>
                                        <?php } ?>
                                    </span>
                                    <strong><?=$shippingMethod['price_formatted']?></strong>
                                </label>
                            <?php } ?>
                        </section>
                    <?php } ?>
                    <?php if (!empty($paymentMethods)) { ?>
                        <section class="webshop-checkout-page__form-section webshop-checkout-page__shipping webshop-checkout-page__payment">
                            <div class="webshop-checkout-page__section-title">
                                <span class="webshop-checkout-page__section-icon"><i class="fas fa-credit-card" aria-hidden="true"></i></span>
                                <div>
                                    <h2><?=t('webshop_checkout_payment_method', 'Betaalmethode')?></h2>
                                    <p><?=t('webshop_checkout_payment_help', 'Kies hoe je deze bestelling wilt betalen. Bij online betalen ga je na het plaatsen van de bestelling door naar Mollie.')?></p>
                                </div>
                            </div>
                            <?php foreach ($paymentMethods as $value => $label) { ?>
                                <label class="webshop-checkout-page__shipping-option">
                                    <input
                                        type="radio"
                                        name="checkout[payment_provider]"
                                        value="<?=htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8')?>"
                                        data-title="<?=htmlspecialchars((string)$label, ENT_QUOTES, 'UTF-8')?>"
                                        data-summary="<?=htmlspecialchars((string)($paymentDescriptions[$value] ?? ''), ENT_QUOTES, 'UTF-8')?>"
                                        <?=(string)$value === $selectedPaymentProvider ? 'checked' : ''?>
                                    >
                                    <span>
                                        <i class="<?=htmlspecialchars((string)($paymentIcons[$value] ?? 'fas fa-credit-card'), ENT_QUOTES, 'UTF-8')?>"></i>
                                        <?=htmlspecialchars((string)$label, ENT_QUOTES, 'UTF-8')?>
                                        <?php if (!empty($paymentDescriptions[$value])) { ?>
                                            <small><?=htmlspecialchars((string)$paymentDescriptions[$value], ENT_QUOTES, 'UTF-8')?></small>
                                        <?php } ?>
                                    </span>
                                </label>
                            <?php } ?>
                        </section>
                    <?php } ?>

                    <div class="webshop-cart-feedback webshop-checkout-page__feedback" data-webshop-checkout-feedback></div>

                    <button class="button button-primary button-wide" type="submit" use-waiting-icon="true">
                        <?=t('webshop_checkout_submit', 'Bestelling plaatsen / betalen')?>
                    </button>
                </form>
            </div>

            <aside class="webshop-checkout-page__summary" style="--cw:4;--cw-sm:12;--cw-xs:12">
                <div class="panel">
                    <div class="panel__header">
                        <h3><?=t('webshop_checkout_summary_title', 'Je bestelling')?></h3>
                    </div>
                    <div class="panel__body">
                        <?php foreach ($items as $item) { ?>
                            <div class="webshop-checkout-page__line">
                                <span class="webshop-checkout-page__line-image">
                                    <img src="<?=$item['product']->getImage()->getResizeUrl(96, 96, 1)?>" alt="<?=htmlspecialchars((string)$item['product']->getTitle(), ENT_QUOTES, 'UTF-8')?>" loading="lazy">
                                </span>
                                <span>
                                    <?=htmlspecialchars((string)$item['product']->getTitle(), ENT_QUOTES, 'UTF-8')?>
                                    <small><?=sprintf(t('webshop_checkout_summary_quantity', '%d stuks'), (int)$item['quantity'])?></small>
                                </span>
                                <strong>&euro; <?=number_format((float)$item['line_total'], 2, ',', '.')?></strong>
                            </div>
                        <?php } ?>
                        <div class="webshop-checkout-page__total">
                            <span><?=t('webshop_cart_subtotal', 'Subtotaal')?></span>
                            <strong><?=$summary['subtotal_formatted'] ?? '&euro; 0,00'?></strong>
                        </div>
                        <div class="webshop-checkout-page__hint">
                            <?=t('webshop_checkout_discount_hint', 'Eventuele kortingscode wordt toegepast bij het plaatsen van de bestelling.')?>
                        </div>
                        <?php if ($selectedShippingMethod) { ?>
                            <div class="webshop-checkout-page__total">
                                <span>
                                    <?=t('webshop_checkout_shipping', 'Verzending')?>
                                    <small data-webshop-summary-shipping-title><?=htmlspecialchars((string)$selectedShippingMethod['method']->getTitle(), ENT_QUOTES, 'UTF-8')?></small>
                                </span>
                                <strong data-webshop-summary-shipping><?=$selectedShippingMethod['price_formatted']?></strong>
                            </div>
                        <?php } ?>
                        <div class="webshop-checkout-page__total webshop-checkout-page__grand-total">
                            <span><?=t('webshop_checkout_estimated_total', 'Geschat totaal')?></span>
                            <strong data-webshop-summary-total>&euro; <?=number_format($subtotal + $initialShippingPrice, 2, ',', '.')?></strong>
                        </div>
                        <div class="webshop-checkout-page__hint" data-webshop-summary-discount>
                            <?php if (trim((string)($checkoutData['discount_code'] ?? '')) !== '') { ?>
                                <?=str_replace('{code}', htmlspecialchars((string)$checkoutData['discount_code'], ENT_QUOTES, 'UTF-8'), t('webshop_checkout_discount_filled_hint', 'Kortingscode "{code}" wordt gecontroleerd bij het plaatsen van de bestelling.'))?>
                            <?php } else { ?>
                                <?=t('webshop_checkout_discount_empty_hint', 'Geen kortingscode ingevuld.')?>
                            <?php } ?>
                        </div>
                        <div class="webshop-checkout-page__hint">
                            <span data-webshop-summary-payment><?=htmlspecialchars((string)($paymentDescriptions[$selectedPaymentProvider] ?? t('webshop_checkout_payment_summary_hint', 'Bij online betalen word je doorgestuurd naar Mollie. Na betaling keer je automatisch terug naar de webshop.')), ENT_QUOTES, 'UTF-8')?></span>
                        </div>
                    </div>
                </div>
            </aside>
        <?php } ?>
    </grid>
</section>
