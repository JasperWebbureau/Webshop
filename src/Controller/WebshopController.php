<?php

namespace Flexgrid\Modules\Webshop\Controller;

use Flexgrid\App\Settings\Settings;
use Flexgrid\Controller\ModuleController;
use Flexgrid\Modules\Webshop\Service\CartService;
use Flexgrid\Modules\Webshop\Service\CheckoutService;
use Flexgrid\Modules\Webshop\Service\FavoriteService;
use Flexgrid\Modules\Webshop\Service\MolliePaymentService;
use Flexgrid\Modules\Webshop\Service\PaymentService;
use Flexgrid\Modules\Webshop\Service\ShippingService;
use Flexgrid\Modules\Webshop\Repository\WebshopPaymentTransactionRepository;
use Flexgrid\Repository\PageRepository;
use Flexgrid\Response\AjaxResponse;
use Flexgrid\Response\TemplateResponse;
use Flexgrid\Utils\Request\Request;

/**
 * @FG\Controller [name=Webshop,type=Webshop, icon=fas fa-shopping-cart]
 */
class WebshopController extends ModuleController
{
    public function productGrid($pageId = 0, $template = 'ProductGrid', $card = 'ProductCard', $mainGroupId = 0, $groupId = 0, $limit = 12, $cardWidth = 4, $color = '', $size = '')
    {
        return (new WebshopProductController())
            ->setFilterAjaxTargetController(get_class($this))
            ->productGrid($pageId, $template, $card, $mainGroupId, $groupId, $limit, $cardWidth, $color, $size);
    }

    public function productPagination($mainGroupId = 0, $groupId = 0, $limit = 12, $color = '', $size = '')
    {
        return (new WebshopProductController())
            ->setFilterAjaxTargetController(get_class($this))
            ->productPagination($mainGroupId, $groupId, $limit, $color, $size);
    }

    public function featuredProducts($pageId = 0, $template = 'ProductGrid', $card = 'ProductCard', $limit = 4, $cardWidth = 3)
    {
        return (new WebshopProductController())->featuredProducts($pageId, $template, $card, $limit, $cardWidth);
    }

    public function getFilterClass($groupId = null, $where = null, array $extraConstraints = [])
    {
        return (new WebshopProductController())->getFilterClass($groupId, $where, $extraConstraints);
    }

    public function productGroupGrid($pageId = 0, $mainGroupId = 0, $limit = 99, $cardWidth = 4, $card = 'ProductGroupCard')
    {
        return (new WebshopProductGroupController())->productGroupGrid($pageId, $mainGroupId, $limit, $cardWidth, $card);
    }

    public function productGroupMenu($limit = 99, $pageId = 0, $mainGroupId = 0)
    {
        return (new WebshopProductGroupController())->productGroupMenu($limit, $pageId, $mainGroupId);
    }

    public function productMainGroupMenu($limit = 99, $pageId = 0, $groupLimit = 99)
    {
        return (new WebshopProductMainGroupController())->productMainGroupMenu($limit, $pageId, $groupLimit);
    }

    /**
     * @FG\Template [name=Winkelwagen samenvatting, icon=fas fa-shopping-basket, html={<div data-type='plugin'><h5>Winkelwagen samenvatting</h5></div>}]
     */
    public function cartSummary()
    {
        $cartService = new CartService();

        return new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/CartSummary/CartSummary.php', [
            'summary' => $cartService->getSummary(),
            'items' => $cartService->getItems(),
        ]);
    }

    /**
     * @FG\Template [name=Winkelwagen pagina, icon=fas fa-shopping-cart, html={<div data-type='plugin'><h5>Winkelwagen pagina</h5></div>},create_override=true]
     */
    public function cartPage()
    {
        $cartService = new CartService();

        return new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/CartPage/CartPage.php', [
            'items' => $cartService->getItems(),
            'summary' => $cartService->getSummary(),
        ]);
    }

    /**
     * @FG\Template [name=Favorieten samenvatting, icon=fas fa-heart, html={<div data-type='plugin'><h5>Favorieten samenvatting</h5></div>}]
     */
    public function favoriteSummary()
    {
        $favoriteService = new FavoriteService();
        $items = $favoriteService->getItems();

        return new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/FavoriteSummary/FavoriteSummary.php', [
            'items' => $items,
            'summary' => [
                'quantity' => count($items),
            ],
        ]);
    }

    /**
     * @FG\Template [name=Favorieten pagina, icon=fas fa-heart, html={<div data-type='plugin'><h5>Favorieten pagina</h5></div>}]
     * @param int $pageId [name=Detail pagina,type=page]
     * @param string $card [name=kaart,type=Template,default=ProductCard2]
     * @param int $cardWidth [name=Kaart breedte,type=int]
     */
    public function favoritePage($pageId = 0, $card = 'ProductCard2', $cardWidth = 4)
    {
        $cardFile = (string)(new WebshopProductController())->getTemplate('Cards', false, $card);
        $moduleCardFile = 'Flexgrid/Modules/Webshop/src/Templates/Cards/' . $card . '.php';
        if ($cardFile === '' || !is_file($cardFile)) {
            $cardFile = is_file($moduleCardFile) ? $moduleCardFile : 'Flexgrid/Modules/Webshop/src/Templates/Cards/ProductCard2.php';
        }

        return new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/FavoritePage/FavoritePage.php', [
            'entities' => (new FavoriteService())->getItems(),
            'card' => $cardFile,
            'pageId' => (int)$pageId,
            'cardWidth' => (int)$cardWidth ?: 4,
            'parentWidth' => 12,
        ]);
    }

    /**
     * @FG\Template [name=Checkout pagina, icon=fas fa-credit-card, html={<div data-type='plugin'><h5>Checkout pagina</h5></div>}]
     */
    public function checkoutPage()
    {
        if((int)$_REQUEST['transaction_id'] > 0){
            return $this->paymentReturn();
        }
        $cartService = new CartService();

        return new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/CheckoutPage/CheckoutPage.php', [
            'items' => $cartService->getItems(),
            'summary' => $cartService->getSummary(),
            'shippingMethods' => (new ShippingService())->getAvailableMethods((float)$cartService->getSummary()['subtotal']),
            'paymentMethods' => (new MolliePaymentService())->getPaymentMethods(),
            'defaultPaymentProvider' => $this->getDefaultPaymentProvider(),
            'checkoutData' => $this->getCheckoutSessionData(),
        ]);
    }

    public function addToCart()
    {
        $request = new Request();
        $productId = (int)$request->get('product_id', 0);
        $quantity = (int)$request->get('quantity', 1);
        $result = (new CartService())->addProduct($productId, $quantity);

        $response = new AjaxResponse();
        $response->success = (bool)$result['success'];
        $response->cart = $result['cart'];

        if (!$result['success']) {
            $response->error = (string)$result['message'];
        }

        $response->setContainer(
            '[data-webshop-cart-feedback]',
            $this->renderCartFeedback((bool)$result['success'], (string)$result['message']),
            false,
            $result['success'] ? 'is-success' : 'is-error'
        );
        $response->setContainer('.webshop-cart-summary', (string)$this->cartSummary(), true);
        $response->setContainer('.js-webshop-cart-count', (string)($result['cart']['quantity'] ?? 0));

        return $response;
    }

    public function updateCartQuantity()
    {
        $request = new Request();
        $result = (new CartService())->setQuantity(
            (int)$request->get('product_id', 0),
            (int)$request->get('quantity', 0)
        );

        return $this->cartAjaxResponse($result);
    }

    public function removeFromCart()
    {
        $request = new Request();
        $result = (new CartService())->removeProduct(
            (int)$request->get('product_id', 0)
        );

        return $this->cartAjaxResponse($result);
    }

    public function toggleFavorite()
    {
        $request = new Request();
        $productId = (int)$request->get('product_id', 0);
        $product = (new WebshopProductController())->getRepository()->findById($productId);

        if (!$product || (int)$product->getId() <= 0) {
            $response = new AjaxResponse();
            $response->success = false;
            $response->error = 'Product niet gevonden.';
            return $response;
        }

        $favoriteService = new FavoriteService();
        $result = $favoriteService->toggleProduct($productId);

        $response = new AjaxResponse();
        $response->success = (bool)$result['success'];
        $response->isFavorite = (bool)$result['isFavorite'];
        $response->favorites = $result['favorites'];

        if (empty($result['success'])) {
            $response->error = (string)$result['message'];
            return $response;
        }

        $response->setContainer(
            '[data-webshop-favorite-form="' . $productId . '"]',
            (string)new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/ProductGrid/FavoriteButton.php', [
                'product' => $product,
                'isFavorite' => (bool)$result['isFavorite'],
            ]),
            true
        );
        $response->setContainer('.webshop-favorite-summary', (string)$this->favoriteSummary(), true);
        $response->setContainer('.js-webshop-favorite-count', (string)count($favoriteService->getItems()));
        $response->setContainer('.webshop-favorite-page', (string)$this->favoritePage(), true);

        return $response;
    }

    public function clearCart()
    {
        $cartService = new CartService();
        $cartService->clear();

        return $this->cartAjaxResponse([
            'success' => true,
            'message' => 'Winkelwagen geleegd.',
            'cart' => $cartService->getSummary(),
        ]);
    }

    public function submitCheckout()
    {
        $request = new Request('checkout');
        $data = $this->mergeCheckoutSessionData($request->getAll());
        $result = (new CheckoutService())->createOrder($data);

        $response = new AjaxResponse();
        $response->success = (bool)($result['success'] ?? false);

        if (empty($result['success'])) {
            $response->error = (string)($result['message'] ?? 'De bestelling kon niet worden aangemaakt.');
            $response->setContainer(
                '[data-webshop-checkout-feedback]',
                $this->renderCartFeedback(false, (string)$response->error)
            );
            return $response;
        }

        if (($result['paymentProvider'] ?? 'manual') === 'mollie') {
            $payment = (new MolliePaymentService())->startPayment($result['order'], $result['paymentTransaction']);
            if (!empty($payment['success']) && trim((string)($payment['checkoutUrl'] ?? '')) !== '') {
                $response->redirect = (string)$payment['checkoutUrl'];
                return $response;
            }

            if (!empty($result['paymentTransaction'])) {
                (new PaymentService())->updateTransactionStatus((int)$result['paymentTransaction']->getId(), 'failed');
            }

            $response->setContainer(
                '[data-webshop-checkout-feedback]',
                $this->renderCartFeedback(false, (string)($payment['message'] ?? 'Online betaling kon niet worden gestart.'))
            );
            return $response;
        }

        $this->clearCheckoutSessionData();

        $response->setContainer(
            '.webshop-checkout-page',
            (string)new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/CheckoutPage/Success.php', [
                'order' => $result['order'],
            ]),
            true
        );
        $response->setContainer('.webshop-cart-summary', (string)$this->cartSummary(), true);
        $response->setContainer('.js-webshop-cart-count', '0');

        return $response;
    }

    public function saveCheckoutSession()
    {
        $request = new Request('checkout');
        $this->mergeCheckoutSessionData($request->getAll());

        $response = new AjaxResponse();
        $response->success = true;
        $response->setContainer(
            '[data-webshop-checkout-save-feedback]',
            '<span>' . t('webshop_checkout_autosave_saved', 'Opgeslagen') . '</span>'
        );

        return $response;
    }
    /**
     * @FG\Template [name=Checkout bedankt pagina, icon=fas fa-credit-card, html={<div data-type='plugin'><h5>Checkout bedankt pagina</h5></div>}]
     */
    public function paymentReturn()
    {
        $request = new Request();
        $transaction = (new WebshopPaymentTransactionRepository())->findById((int)$request->get('transaction_id', 0));

        if ($transaction && (int)$transaction->getId() > 0 && trim((string)$transaction->getProviderReference()) !== '') {
            (new MolliePaymentService())->syncPayment((string)$transaction->getProviderReference());
            $transaction = (new WebshopPaymentTransactionRepository())->findById((int)$transaction->getId());
        }

        if ($transaction && (string)$transaction->getStatus() === 'paid') {
            (new CartService())->clear();
            $this->clearCheckoutSessionData();
        }

        return new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/Payment/Return.php', [
            'transaction' => $transaction,
            'order' => $transaction && (int)$transaction->getId() > 0 ? $transaction->getWebshopOrderParent() : null,
        ]);
    }

    public function paymentWebhook()
    {
        $paymentId = (string)($_POST['id'] ?? $_GET['id'] ?? '');
        $result = (new MolliePaymentService())->syncPayment($paymentId);

        header('Content-Type: text/plain; charset=utf-8');
        echo !empty($result['success']) ? 'ok' : 'missing';
        die();
    }

    protected function cartAjaxResponse(array $result): AjaxResponse
    {
        $response = new AjaxResponse();
        $response->success = (bool)($result['success'] ?? false);
        $response->cart = $result['cart'] ?? (new CartService())->getSummary();

        if (empty($result['success'])) {
            $response->error = (string)($result['message'] ?? 'De winkelwagen kon niet worden bijgewerkt.');
        }

        $response->setContainer('.webshop-cart-page', (string)$this->cartPage(), true);
        $response->setContainer('.webshop-cart-summary', (string)$this->cartSummary(), true);
        $response->setContainer('.js-webshop-cart-count', (string)($response->cart['quantity'] ?? 0));

        return $response;
    }

    protected function renderCartFeedback(bool $success, string $message): string
    {
        $class = $success ? 'webshop-cart-feedback__message webshop-cart-feedback__message--success' : 'webshop-cart-feedback__message webshop-cart-feedback__message--error';

        return '<div class="' . $class . '">' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</div>';
    }

    protected function getCheckoutSessionData(): array
    {
        $data = $_SESSION['webshop_checkout'] ?? [];

        return is_array($data) ? $this->filterCheckoutSessionData($data) : [];
    }

    protected function mergeCheckoutSessionData(array $data): array
    {
        $current = $this->getCheckoutSessionData();
        $filtered = $this->filterCheckoutSessionData($data);

        $_SESSION['webshop_checkout'] = array_merge($current, $filtered);

        return $_SESSION['webshop_checkout'];
    }

    protected function clearCheckoutSessionData(): void
    {
        unset($_SESSION['webshop_checkout']);
    }

    protected function filterCheckoutSessionData(array $data): array
    {
        $fields = array_flip($this->getCheckoutSessionFieldKeys());
        $filtered = [];

        foreach ($data as $key => $value) {
            if (!isset($fields[$key])) {
                continue;
            }

            if (is_array($value) || is_object($value)) {
                continue;
            }

            $filtered[$key] = trim(strip_tags((string)$value));
        }

        return $filtered;
    }

    protected function getCheckoutSessionFieldKeys(): array
    {
        return [
            'first_name',
            'last_name',
            'email',
            'phone',
            'company_name',
            'address',
            'postal_code',
            'city',
            'note',
            'discount_code',
            'shipping_method_id',
            'payment_provider',
        ];
    }

    static function getProductGridUrl()
    {
        return self::getConfiguredPageUrl('page_product_grid_page_id', 'webshop product grid pagina');
    }

    static function getShoppingCartUrl()
    {
        return self::getConfiguredPageUrl('page_webshop_cart_id', 'webshop winkelwagen pagina');
    }

    static function getFavoriteUrl()
    {
        return self::getConfiguredPageUrl('page_webshop_favorites_id', 'webshop favorieten pagina');
    }

    static function getCheckoutUrl()
    {
        return self::getConfiguredPageUrl('page_webshop_checkout_id', 'webshop checkout pagina');
    }

    static function getPaymentReturnUrl(int $transactionId = 0)
    {
        new Settings();
        $setting = Settings::get('page_webshop_paymentreturn_id', ['value'=>0, "label"=> "webshop payment return pagina"]);
        $pageRepository = new PageRepository();
        $url = '';
        if ((int)($setting['value'] ?? 0) > 0) {
            $url = (string)$pageRepository->findById($setting['value'])->getUrl();
        }

        $url = trim($url) !== '' ? __DOMAIN__ . '/' . ltrim($url, '/') : __DOMAIN__ . '/Flexgrid/Webshop/paymentReturn';
        if ($transactionId <= 0) {
            return $url;
        }

        return $url . (strpos($url, '?') === false ? '?' : '&') . 'transaction_id=' . $transactionId;
    }

    static function getProductGridPageId(): int
    {
        return self::getConfiguredPageId('page_product_grid_page_id', 'webshop product grid pagina');
    }

    protected static function getConfiguredPageUrl(string $settingKey, string $label): string
    {
        new Settings();
        $page = (new PageRepository())->findById(self::getConfiguredPageId($settingKey, $label));
        $url = $page ? trim((string)$page->getUrl()) : '';

        if ($url === '') {
            throw new \RuntimeException('De page setting "' . $settingKey . '" verwijst niet naar een geldige pagina.');
        }

        if (preg_match('#^https?://#i', $url)) {
            return $url;
        }

        return rtrim(__DOMAIN__, '/') . '/' . ltrim($url, '/');
    }

    protected static function getConfiguredPageId(string $settingKey, string $label): int
    {
        new Settings();
        $setting = Settings::get($settingKey, [
            'value' => 0,
            'label' => $label,
        ]);
        $pageId = (int)($setting['value'] ?? 0);

        if ($pageId <= 0) {
            throw new \RuntimeException('De page setting "' . $settingKey . '" is niet ingesteld.');
        }

        return $pageId;
    }

    protected function getDefaultPaymentProvider(): string
    {
        new Settings();
        $setting = Settings::get('webshop_default_payment_provider', [
            'value' => 'manual',
            'label' => 'Standaard betaalmethode',
        ]);

        $provider = (string)($setting['value'] ?? 'manual');
        if ($provider === 'mollie' && (new MolliePaymentService())->isEnabled()) {
            return 'mollie';
        }

        return 'manual';
    }
}
