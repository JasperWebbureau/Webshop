(function () {
    var autosaveTimers = new WeakMap();

    function formatMoney(value) {
        return '&euro; ' + Number(value || 0).toFixed(2).replace('.', ',');
    }

    function updateSummary(root) {
        var subtotal = parseFloat(root.getAttribute('data-subtotal') || '0');
        var shipping = root.querySelector('input[name="checkout[shipping_method_id]"]:checked');
        var payment = root.querySelector('input[name="checkout[payment_provider]"]:checked');
        var discount = root.querySelector('input[name="checkout[discount_code]"]');
        var shippingPrice = shipping ? parseFloat(shipping.getAttribute('data-price') || '0') : 0;
        var shippingLabel = shipping ? shipping.getAttribute('data-price-label') : '';
        var shippingTitle = shipping ? shipping.getAttribute('data-title') : '';
        var paymentSummary = payment ? payment.getAttribute('data-summary') : '';
        var discountValue = discount ? discount.value.trim() : '';

        var shippingTarget = root.querySelector('[data-webshop-summary-shipping]');
        if (shippingTarget) {
            shippingTarget.innerHTML = shippingLabel || formatMoney(shippingPrice);
        }

        var shippingTitleTarget = root.querySelector('[data-webshop-summary-shipping-title]');
        if (shippingTitleTarget) {
            shippingTitleTarget.textContent = shippingTitle;
        }

        var totalTarget = root.querySelector('[data-webshop-summary-total]');
        if (totalTarget) {
            totalTarget.innerHTML = formatMoney(subtotal + shippingPrice);
        }

        var paymentTarget = root.querySelector('[data-webshop-summary-payment]');
        if (paymentTarget && paymentSummary) {
            paymentTarget.textContent = paymentSummary;
        }

        var discountTarget = root.querySelector('[data-webshop-summary-discount]');
        if (discountTarget) {
            discountTarget.textContent = discountValue
                ? (root.getAttribute('data-discount-filled') || '').replace('{code}', discountValue)
                : (root.getAttribute('data-discount-empty') || '');
        }
    }

    function submitSessionForm(form) {
        if (!form || form.getAttribute('data-webshop-checkout-saving') === 'true') {
            return;
        }

        form.setAttribute('data-webshop-checkout-saving', 'true');
        form.dispatchEvent(new Event('submit', {
            bubbles: true,
            cancelable: true
        }));

        window.setTimeout(function () {
            form.removeAttribute('data-webshop-checkout-saving');
        }, 250);
    }

    function scheduleSessionSave(form) {
        if (!form) {
            return;
        }

        window.clearTimeout(autosaveTimers.get(form));
        autosaveTimers.set(form, window.setTimeout(function () {
            submitSessionForm(form);
        }, 500));
    }

    function saveSingleField(root, field) {
        var action = root.getAttribute('data-save-action');

        if (!action || typeof Ajax === 'undefined' || !field || !field.name) {
            return;
        }

        var ajax = new Ajax();
        ajax.set('action', action);
        ajax.set(field.name, field.value);
        ajax.go(false);
    }

    function copyInputToSubmitForm(input, submitForm) {
        if (!input.name || input.disabled || input.name === 'action') {
            return;
        }

        if ((input.type === 'radio' || input.type === 'checkbox') && !input.checked) {
            return;
        }

        var hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = input.name;
        hidden.value = input.value;
        hidden.setAttribute('data-webshop-checkout-hidden-step', 'true');
        submitForm.appendChild(hidden);
    }

    function syncStepFieldsIntoSubmitForm(root) {
        var submitForm = root.querySelector('[data-webshop-checkout-submit-form]');

        if (!submitForm) {
            return;
        }

        submitForm.querySelectorAll('[data-webshop-checkout-hidden-step]').forEach(function (input) {
            input.remove();
        });

        root.querySelectorAll('[data-webshop-checkout-session-form]').forEach(function (form) {
            form.querySelectorAll('input[name], textarea[name], select[name]').forEach(function (input) {
                copyInputToSubmitForm(input, submitForm);
            });
        });
    }

    function validateStepForms(root) {
        var forms = root.querySelectorAll('[data-webshop-checkout-session-form]');
        var invalidForm = null;

        forms.forEach(function (form) {
            if (!invalidForm && typeof form.checkValidity === 'function' && !form.checkValidity()) {
                invalidForm = form;
            }
        });

        if (invalidForm && typeof invalidForm.reportValidity === 'function') {
            invalidForm.reportValidity();
        }

        return !invalidForm;
    }

    function initCheckout(root) {
        if (!root || root.getAttribute('data-webshop-checkout-initialized') === 'true') {
            return;
        }

        root.setAttribute('data-webshop-checkout-initialized', 'true');

        root.addEventListener('submit', function (event) {
            if (event.target.matches('[data-webshop-checkout-submit-form]')) {
                if (!validateStepForms(root)) {
                    event.preventDefault();
                    event.stopImmediatePropagation();
                    return;
                }

                syncStepFieldsIntoSubmitForm(root);
            }
        }, true);

        root.addEventListener('change', function (event) {
            var sessionForm = event.target.closest('[data-webshop-checkout-session-form]');

            if (sessionForm) {
                submitSessionForm(sessionForm);
            }

            if (event.target.matches('input[name="checkout[shipping_method_id]"], input[name="checkout[payment_provider]"]')) {
                saveSingleField(root, event.target);
            }

            updateSummary(root);
        });

        root.addEventListener('input', function (event) {
            var sessionForm = event.target.closest('[data-webshop-checkout-session-form]');

            if (sessionForm) {
                scheduleSessionSave(sessionForm);
            }

            if (event.target.matches('input[name="checkout[discount_code]"]')) {
                updateSummary(root);
            }
        });

        updateSummary(root);
    }

    function initAll() {
        document.querySelectorAll('[data-webshop-checkout]').forEach(initCheckout);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }

    if (window.jQuery) {
        window.jQuery(document).off('fg:ajax-success.webshopCheckout').on('fg:ajax-success.webshopCheckout', function () {
            initAll();
        });
    }
})();
