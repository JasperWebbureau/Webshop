<?php

namespace Flexgrid\Modules\Webshop\Controller;

use Flexgrid\App\Settings\Settings;
use Flexgrid\Flexgrid;
use Flexgrid\Modules\Webshop\Repository\WebshopCustomerRepository;
use Flexgrid\Modules\Webshop\Repository\WebshopOrderLineRepository;
use Flexgrid\Modules\Webshop\Repository\WebshopOrderRepository;
use Flexgrid\Modules\Webshop\Repository\WebshopInvoiceLineRepository;
use Flexgrid\Modules\Webshop\Repository\WebshopInvoiceRepository;
use Flexgrid\Modules\Webshop\Service\InvoiceService;
use Flexgrid\Modules\Webshop\Entity\WebshopMoodboard;
use Flexgrid\Modules\Webshop\Entity\WebshopMoodboardItem;
use Flexgrid\Modules\Webshop\Repository\WebshopMoodboardItemRepository;
use Flexgrid\Modules\Webshop\Repository\WebshopMoodboardRepository;
use Flexgrid\Modules\Webshop\Repository\WebshopPaymentTransactionRepository;
use Flexgrid\Modules\Webshop\Repository\WebshopProductMainGroupRepository;
use Flexgrid\Modules\Webshop\Repository\WebshopProductGroupRepository;
use Flexgrid\Modules\Webshop\Repository\WebshopProductRepository;
use Flexgrid\Modules\Webshop\Repository\WebshopStockMutationRepository;
use Flexgrid\Modules\Webshop\Repository\WebshopTaxRateRepository;
use Flexgrid\Modules\Webshop\Service\PaymentService;
use Flexgrid\Modules\Webshop\Service\InvoicePdfService;
use Flexgrid\Modules\Webshop\Service\OrderStatusService;
use Flexgrid\Modules\Webshop\Service\ReportService;
use Flexgrid\Modules\Webshop\Service\ProductAiGeneratorService;
use Flexgrid\Response\AjaxResponse;
use Flexgrid\Response\PageResponse;
use Flexgrid\Response\TemplateResponse;
use Flexgrid\Utils\Request\Request;

/**
 * @FG\Controller [name=WebshopAdmin,type=Flexgrid, icon=fas fa-shopping-cart]
 */
class WebshopAdminController
{
    public function __construct()
    {

        if(Flexgrid::getApp()->currentController != 'Flexgrid\App\Dashboard\Dashboard'){


            $this->getNavigation();;
            new \Flexgrid\Html\Element\Panel();
        }
    }

    /**
     * @FG\Template [Flexgrid=true,Flexgridpanel=true]
     */
    public function getDashboardPanel()
    {
        return new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/DashboardPanel.php');
    }

    public function dashboard()
    {
        $orderRepository = new WebshopOrderRepository();

        return new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/Admin/Dashboard.php', [
            'recentOrders' => $orderRepository->getRecent(6),
            'stats' => $orderRepository->getDashboardStats(),
        ]);
    }

    private function getNavigation()
    {

        Flexgrid::getApp()->appendMainHeader((string)new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/Admin/Navigation/Navigation.php'));
    }

    public function orders()
    {
        PageResponse::addAsset('Flexgrid/Modules/Webshop/src/Templates/Admin/Orders/Css/Orders.scss');
        $filters = $this->getOrderFilters();

        return new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/Admin/Orders/Index.php', [
            'orders' => (new WebshopOrderRepository())->search($filters, 100),
            'filters' => $filters,
            'statusOptions' => $this->getOrderStatusOptions(),
            'paymentStatusOptions' => $this->getPaymentStatusOptions(),
        ]);
    }

    public function orderDetail($args = [])
    {
        PageResponse::addAsset('Flexgrid/Modules/Webshop/src/Templates/Admin/Orders/Css/Orders.scss');

        $orderId = is_array($args) ? (int)($args[0] ?? 0) : (int)$args;
        $order = (new WebshopOrderRepository())->findById($orderId);

        if (!$order || (int)$order->getId() <= 0) {
            return $this->orders();
        }

        return new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/Admin/Orders/Detail.php', [
            'order' => $order,
            'lines' => (new WebshopOrderLineRepository())->getByOrderId((int)$order->getId()),
            'invoice' => (new WebshopInvoiceRepository())->findByOrderId((int)$order->getId()),
            'paymentTransactions' => (new WebshopPaymentTransactionRepository())->getByOrderId((int)$order->getId()),
            'stockMutations' => (new WebshopStockMutationRepository())->getByOrderId((int)$order->getId()),
            'statusOptions' => (new OrderStatusService())->getOrderStatusOptions(),
            'paymentStatusOptions' => (new OrderStatusService())->getPaymentStatusOptions(),
            'transactionStatusOptions' => (new PaymentService())->getStatusOptions(),
        ]);
    }

    public function customers()
    {
        PageResponse::addAsset('Flexgrid/Modules/Webshop/src/Templates/Admin/Orders/Css/Orders.scss');
        $filters = $this->getCustomerFilters();

        return new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/Admin/Customers/Index.php', [
            'customers' => (new WebshopCustomerRepository())->search($filters, 100),
            'filters' => $filters,
        ]);
    }

    public function customerDetail($args = [])
    {
        PageResponse::addAsset('Flexgrid/Modules/Webshop/src/Templates/Admin/Orders/Css/Orders.scss');

        $customerId = is_array($args) ? (int)($args[0] ?? 0) : (int)$args;
        $customer = (new WebshopCustomerRepository())->findById($customerId);

        if (!$customer || (int)$customer->getId() <= 0) {
            return $this->customers();
        }

        $orders = (new WebshopOrderRepository())->getByCustomerId((int)$customer->getId(), 100);

        return new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/Admin/Customers/Detail.php', [
            'customer' => $customer,
            'orders' => $orders,
            'totals' => $this->getCustomerTotals($orders),
        ]);
    }

    public function products()
    {
        PageResponse::addAsset('Flexgrid/Modules/Webshop/src/Templates/Admin/Products/Css/Products.scss');
        $filters = $this->getProductFilters();

        return new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/Admin/Products/Index.php', [
            'products' => (new WebshopProductRepository())->search($filters, 100),
            'filters' => $filters,
            'mainGroupOptions' => $this->getProductMainGroupOptions(true),
            'groupOptions' => $this->getProductGroupOptions(true),
            'statusOptions' => $this->getProductStatusOptions(),
            'activeOptions' => $this->getActiveOptions(),
            'colorOptions' => $this->getProductPropertyOptions('color', t('webshop_admin_products_all_colors', 'Alle kleuren')),
            'sizeOptions' => $this->getProductPropertyOptions('size', t('webshop_admin_products_all_sizes', 'Alle maten')),
        ]);
    }

    public function newProduct()
    {
        PageResponse::addAsset('Flexgrid/Modules/Webshop/src/Templates/Admin/Products/Css/Products.scss');

        return new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/Admin/Products/AiCreate.php', [
            'desiredMarginPercentage' => $this->getWebshopDesiredMarginPercentage(),
        ]);
    }

    public function generateProductWithAi()
    {
        PageResponse::addAsset('Flexgrid/Modules/Webshop/src/Templates/Admin/Products/Css/Products.scss');

        $request = new Request();
        $title = trim((string)$request->get('title', ''));
        $sourceUrl = trim((string)$request->get('source_url', ''));
        $sourceHtml = (string)$request->get('source_html', '');
        $manufacturer = trim((string)$request->get('manufacturer', ''));
        $purchasePrice = $this->normalizeMoney((string)$request->get('purchase_price', '0'));
        $useWebSearch = (int)$request->get('use_web_search', 0) === 1;
        $allowGroupCreate = (int)$request->get('allow_group_create', 0) === 1;
        $allowPurchasePriceEstimate = (int)$request->get('allow_purchase_price_estimate', 0) === 1;
        $allowTitleRewrite = (int)$request->get('allow_title_rewrite', 0) === 1;
        $allowVariantDetection = (int)$request->get('allow_variant_detection', 0) === 1;
        $minimumDescriptionParagraphs = max(1, min(8, (int)$request->get('minimum_description_paragraphs', 2)));
        $desiredMarginPercentage = $this->getWebshopDesiredMarginPercentage();
        $image = $_FILES['image'] ?? [];
        $result = (new ProductAiGeneratorService())->createProduct(
            $title,
            is_array($image) ? $image : [],
            $manufacturer,
            $purchasePrice,
            $useWebSearch,
            $desiredMarginPercentage,
            $allowGroupCreate,
            $allowPurchasePriceEstimate,
            $minimumDescriptionParagraphs,
            $allowTitleRewrite,
            $allowVariantDetection,
            $sourceUrl,
            $sourceHtml
        );

        if (empty($result['success'])) {
            return new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/Admin/Products/AiCreate.php', [
                'error' => (string)($result['message'] ?? 'Product kon niet worden gegenereerd.'),
                'title' => $title,
                'sourceUrl' => $sourceUrl,
                'sourceHtml' => $sourceHtml,
                'manufacturer' => $manufacturer,
                'purchasePrice' => $purchasePrice,
                'useWebSearch' => $useWebSearch,
                'allowGroupCreate' => $allowGroupCreate,
                'allowPurchasePriceEstimate' => $allowPurchasePriceEstimate,
                'allowTitleRewrite' => $allowTitleRewrite,
                'allowVariantDetection' => $allowVariantDetection,
                'minimumDescriptionParagraphs' => $minimumDescriptionParagraphs,
                'desiredMarginPercentage' => $desiredMarginPercentage,
            ]);
        }

        $product = $result['product'];
        header('Location: ' . __DOMAIN__ . '/Flexgrid/WebshopAdmin/productDetail/' . (int)$product->getId());
        exit;
    }

    public function productDetail($args = [])
    {
        PageResponse::addAsset('Flexgrid/Modules/Webshop/src/Templates/Admin/Products/Css/Products.scss');

        $productId = is_array($args) ? (int)($args[0] ?? 0) : (int)$args;
        $product = (new WebshopProductRepository())->findById($productId);

        if (!$product || (int)$product->getId() <= 0) {
            return $this->products();
        }

        return new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/Admin/Products/Detail.php', [
            'product' => $product,
            'groupOptions' => $this->getProductGroupOptions(false),
            'taxRateOptions' => $this->getTaxRateOptions(),
            'statusOptions' => $this->getProductStatusOptions(),
            'activeOptions' => $this->getActiveOptions(),
            'productGridPageId' => $this->getProductGridPageId(),
        ]);
    }

    public function moodboards()
    {
        PageResponse::addAsset('Flexgrid/Modules/Webshop/src/Templates/Admin/Moodboards/Css/Moodboards.scss');
        $request = new Request();

        return new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/Admin/Moodboards/Index.php', [
            'moodboards' => (new WebshopMoodboardRepository())->search(trim((string)$request->get('q', '')), 100),
            'query' => trim((string)$request->get('q', '')),
        ]);
    }

    public function moodboardDetail($args = [])
    {
        PageResponse::addAsset('Flexgrid/Modules/Webshop/src/Templates/Admin/Moodboards/Css/Moodboards.scss');
        PageResponse::addAsset('Flexgrid/Modules/Webshop/src/Templates/Admin/Moodboards/Js/Moodboards.js');

        $moodboardId = is_array($args) ? (int)($args[0] ?? 0) : (int)$args;
        $repository = new WebshopMoodboardRepository();
        $moodboard = $moodboardId > 0 ? $repository->findById($moodboardId) : new WebshopMoodboard();

        if (!$moodboard || ($moodboardId > 0 && (int)$moodboard->getId() <= 0)) {
            return $this->moodboards();
        }

        return new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/Admin/Moodboards/Detail.php', [
            'moodboard' => $moodboard,
            'items' => (int)$moodboard->getId() > 0 ? (new WebshopMoodboardItemRepository())->getByMoodboardId((int)$moodboard->getId()) : [],
            'productOptions' => $this->getMoodboardProductOptions(),
            'productGridPageId' => $this->getProductGridPageId(),
        ]);
    }

    public function saveMoodboard()
    {
        $repository = new WebshopMoodboardRepository();
        $request = new Request();
        $moodboardId = (int)$request->get('id', 0);
        $moodboard = $moodboardId > 0 ? $repository->findById($moodboardId) : new WebshopMoodboard();

        if (!$moodboard || ($moodboardId > 0 && (int)$moodboard->getId() <= 0)) {
            header('Location: ' . __DOMAIN__ . '/Flexgrid/WebshopAdmin/moodboards');
            exit;
        }

        $moodboard
            ->setTitle(trim((string)$request->get('title', '')))
            ->setEyebrow(trim((string)$request->get('eyebrow', '')))
            ->setIntro(trim(strip_tags((string)$request->get('intro', ''))))
            ->setButtonText(trim((string)$request->get('button_text', '')))
            ->setIsActive((int)$request->get('is_active', 0) === 1 ? 1 : 0);

        $moodboard = $repository->add($moodboard);
        $this->saveMoodboardItems((int)$moodboard->getId());

        header('Location: ' . __DOMAIN__ . '/Flexgrid/WebshopAdmin/moodboardDetail/' . (int)$moodboard->getId());
        exit;
    }

    public function deleteMoodboard()
    {
        $request = new Request();
        $moodboardId = (int)$request->get('id', 0);
        $repository = new WebshopMoodboardRepository();
        $moodboard = $repository->findById($moodboardId);

        if ($moodboard && (int)$moodboard->getId() > 0) {
            $itemRepository = new WebshopMoodboardItemRepository();
            foreach ($itemRepository->getByMoodboardId((int)$moodboard->getId()) as $item) {
                $itemRepository->delete($item);
            }

            $repository->delete($moodboard);
        }

        header('Location: ' . __DOMAIN__ . '/Flexgrid/WebshopAdmin/moodboards');
        exit;
    }

    public function invoices()
    {
        PageResponse::addAsset('Flexgrid/Modules/Webshop/src/Templates/Admin/Invoices/Css/Invoices.scss');

        return new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/Admin/Invoices/Index.php', [
            'invoices' => (new WebshopInvoiceRepository())->getRecent(100),
        ]);
    }

    public function invoiceDetail($args = [])
    {
        PageResponse::addAsset('Flexgrid/Modules/Webshop/src/Templates/Admin/Invoices/Css/Invoices.scss');

        $invoiceId = is_array($args) ? (int)($args[0] ?? 0) : (int)$args;
        $invoice = (new WebshopInvoiceRepository())->findById($invoiceId);

        if (!$invoice || (int)$invoice->getId() <= 0) {
            return $this->invoices();
        }

        return new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/Admin/Invoices/Detail.php', [
            'invoice' => $invoice,
            'lines' => (new WebshopInvoiceLineRepository())->getByInvoiceId((int)$invoice->getId()),
        ]);
    }

    public function reports()
    {
        PageResponse::addAsset('Flexgrid/Modules/Webshop/src/Templates/Admin/Reports/Css/Reports.scss');

        return new TemplateResponse(
            'Flexgrid/Modules/Webshop/src/Templates/Admin/Reports/Index.php',
            (new ReportService())->getOverview()
        );
    }

    public function settings()
    {
        PageResponse::addAsset('Flexgrid/Modules/Webshop/src/Templates/Admin/Settings/Css/Settings.scss');

        return new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/Admin/Settings/Index.php', [
            'settings' => $this->getWebshopSettings(),
        ]);
    }

    public function saveSettings()
    {
        new Settings();
        $request = new Request('webshop_settings');
        $values = $request->getAll();

        foreach ($this->getWebshopSettings() as $section) {
            foreach ($section['fields'] as $key => $field) {
                $value = $values[$key] ?? ($field['type'] === 'checkbox' ? 0 : $field['value']);
                Settings::set($key, $this->normalizeSettingValue($value, $field));
            }
        }

        (new Settings())->save();

        $response = new AjaxResponse();
        $response->success = true;
        $response->setContainer(
            '[data-webshop-settings-feedback]',
            '<div class="webshop-admin-settings__message">' . t('webshop_admin_settings_saved', 'Instellingen opgeslagen.') . '</div>'
        );

        return $response;
    }

    public function createInvoice()
    {
        $request = new Request();
        $result = (new InvoiceService())->createForOrder((int)$request->get('order_id', 0));
        $response = new AjaxResponse();
        $response->success = (bool)($result['success'] ?? false);

        if (empty($result['success'])) {
            $response->error = (string)($result['message'] ?? 'Factuur kon niet worden aangemaakt.');
            return $response;
        }

        $invoice = $result['invoice'];
        $response->redirect = __DOMAIN__ . '/Flexgrid/WebshopAdmin/invoiceDetail/' . (int)$invoice->getId();

        return $response;
    }

    public function generateInvoicePdf()
    {
        $request = new Request();
        $invoice = (new WebshopInvoiceRepository())->findById((int)$request->get('invoice_id', 0));
        $response = new AjaxResponse();

        if (!$invoice || (int)$invoice->getId() <= 0) {
            $response->success = false;
            $response->error = 'Factuur niet gevonden.';
            return $response;
        }

        $result = (new InvoicePdfService())->generate($invoice);
        $response->success = (bool)($result['success'] ?? false);

        if (empty($result['success'])) {
            $response->error = (string)($result['message'] ?? 'PDF kon niet worden aangemaakt.');
            return $response;
        }

        $response->redirect = __DOMAIN__ . '/Flexgrid/WebshopAdmin/invoiceDetail/' . (int)$invoice->getId();

        return $response;
    }

    public function updateOrderStatus()
    {
        $request = new Request();
        $order = (new WebshopOrderRepository())->findById((int)$request->get('order_id', 0));
        $status = (string)$request->get('status', '');
        $response = new AjaxResponse();

        if (!$order || (int)$order->getId() <= 0) {
            $response->success = false;
            $response->error = 'Orderstatus kon niet worden opgeslagen.';
            return $response;
        }

        $result = (new OrderStatusService())->updateOrderStatus($order, $status);
        $response->success = (bool)($result['success'] ?? false);
        if (empty($result['success'])) {
            $response->error = (string)($result['message'] ?? 'Orderstatus kon niet worden opgeslagen.');
        }

        return $response;
    }

    public function updatePaymentStatus()
    {
        $request = new Request();
        $order = (new WebshopOrderRepository())->findById((int)$request->get('order_id', 0));
        $status = (string)$request->get('payment_status', '');
        $response = new AjaxResponse();

        if (!$order || (int)$order->getId() <= 0) {
            $response->success = false;
            $response->error = 'Betaalstatus kon niet worden opgeslagen.';
            return $response;
        }

        $result = (new OrderStatusService())->updatePaymentStatus($order, $status);
        $response->success = (bool)($result['success'] ?? false);
        if (empty($result['success'])) {
            $response->error = (string)($result['message'] ?? 'Betaalstatus kon niet worden opgeslagen.');
        }

        return $response;
    }

    public function updateShippingInfo()
    {
        $request = new Request();
        $orderRepository = new WebshopOrderRepository();
        $order = $orderRepository->findById((int)$request->get('order_id', 0));
        $response = new AjaxResponse();

        if (!$order || (int)$order->getId() <= 0) {
            $response->success = false;
            $response->error = 'Verzendgegevens konden niet worden opgeslagen.';
            return $response;
        }

        $order
            ->setShippingTrackingCode((string)$request->get('shipping_tracking_code', ''))
            ->setShippingTrackingUrl((string)$request->get('shipping_tracking_url', ''));

        $markShipped = (int)$request->get('mark_shipped', 0) === 1;
        if ($markShipped && (int)$order->getShippedAt() <= 0) {
            $order->setShippedAt(time());
        }

        $order = $orderRepository->add($order);

        if ($markShipped) {
            $result = (new OrderStatusService())->updateOrderStatus($order, 'shipped');
            $response->success = (bool)($result['success'] ?? false);
            if (empty($result['success'])) {
                $response->error = (string)($result['message'] ?? 'Order kon niet als verzonden worden gemarkeerd.');
                return $response;
            }
        } else {
            $response->success = true;
        }

        $response->setContainer(
            '[data-webshop-shipping-feedback]',
            '<div class="webshop-admin-order-detail__message">' . t('webshop_admin_order_shipping_saved', 'Verzendgegevens opgeslagen.') . '</div>'
        );

        return $response;
    }

    public function updatePaymentTransactionStatus()
    {
        $request = new Request();
        $result = (new PaymentService())->updateTransactionStatus(
            (int)$request->get('transaction_id', 0),
            (string)$request->get('status', ''),
            (string)$request->get('provider_reference', '')
        );

        $response = new AjaxResponse();
        $response->success = (bool)($result['success'] ?? false);

        if (empty($result['success'])) {
            $response->error = (string)($result['message'] ?? 'Betaaltransactie kon niet worden opgeslagen.');
        }

        return $response;
    }

    protected function getOrderStatusOptions(): array
    {
        return (new OrderStatusService())->getOrderStatusOptions();
    }

    protected function getPaymentStatusOptions(): array
    {
        return (new OrderStatusService())->getPaymentStatusOptions();
    }

    protected function getOrderFilters(): array
    {
        $request = new Request();
        $statusOptions = $this->getOrderStatusOptions();
        $paymentStatusOptions = $this->getPaymentStatusOptions();
        $status = trim((string)$request->get('status', ''));
        $paymentStatus = trim((string)$request->get('payment_status', ''));

        return [
            'q' => trim((string)$request->get('q', '')),
            'status' => isset($statusOptions[$status]) ? $status : '',
            'payment_status' => isset($paymentStatusOptions[$paymentStatus]) ? $paymentStatus : '',
        ];
    }

    protected function getCustomerFilters(): array
    {
        $request = new Request();

        return [
            'q' => trim((string)$request->get('q', '')),
        ];
    }

    protected function getProductFilters(): array
    {
        $request = new Request();
        $statusOptions = $this->getProductStatusOptions();
        $activeOptions = $this->getActiveOptions();
        $status = trim((string)$request->get('status', ''));
        $active = trim((string)$request->get('active', ''));
        $mainGroupId = max(0, (int)$request->get('main_group_id', 0));
        $color = trim((string)$request->get('color', ''));
        $size = trim((string)$request->get('size', ''));

        return [
            'q' => trim((string)$request->get('q', '')),
            'main_group_id' => $mainGroupId,
            'group_id' => max(0, (int)$request->get('group_id', 0)),
            'status' => isset($statusOptions[$status]) ? $status : '',
            'active' => isset($activeOptions[$active]) ? $active : '',
            'color' => $color,
            'size' => $size,
        ];
    }

    protected function getProductGroupOptions(bool $includeAll = false): array
    {
        $options = [0 => $includeAll ? t('webshop_admin_products_all_groups', 'Alle groepen') : '-'];
        foreach ((new WebshopProductGroupRepository())->getActive() as $group) {
            $prefix = '';
            if (method_exists($group, 'getWebshopProductMainGroupValue')) {
                $mainGroupTitle = trim((string)$group->getWebshopProductMainGroupValue());
                $prefix = $mainGroupTitle !== '' ? $mainGroupTitle . ' / ' : '';
            }

            $options[(int)$group->getId()] = $prefix . (string)$group->getTitle();
        }

        return $options;
    }

    protected function getProductMainGroupOptions(bool $includeAll = false): array
    {
        $options = [0 => $includeAll ? t('webshop_admin_products_all_main_groups', 'Alle hoofdgroepen') : '-'];
        foreach ((new WebshopProductMainGroupRepository())->getActive() as $group) {
            $options[(int)$group->getId()] = (string)$group->getTitle();
        }

        return $options;
    }

    protected function getTaxRateOptions(): array
    {
        $options = [0 => '-'];
        foreach ((new WebshopTaxRateRepository())->getActive() as $taxRate) {
            $options[(int)$taxRate->getId()] = (string)$taxRate->getTitle() . ' (' . number_format((float)$taxRate->getRate(), 2, ',', '.') . '%)';
        }

        return $options;
    }

    protected function getProductPropertyOptions(string $property, string $emptyLabel): array
    {
        return ['' => $emptyLabel] + (new WebshopProductRepository())->getPropertyOptions($property);
    }

    protected function getMoodboardProductOptions(): array
    {
        $options = [0 => '-'];
        foreach ((new WebshopProductRepository())->setPagination(false)->getAll(500, null, null, true, 'title:ASC') as $product) {
            if (!$product || (int)$product->getId() <= 0) {
                continue;
            }

            $label = (string)$product->getTitle();
            if (trim((string)$product->getSku()) !== '') {
                $label .= ' (' . (string)$product->getSku() . ')';
            }

            $options[(int)$product->getId()] = $label;
        }

        return $options;
    }

    protected function getProductStatusOptions(): array
    {
        return [
            'draft' => 'Concept',
            'published' => 'Gepubliceerd',
            'archived' => 'Archief',
        ];
    }

    protected function getActiveOptions(): array
    {
        return [
            '' => t('webshop_admin_products_all_active_states', 'Alle actief statussen'),
            '1' => t('webshop_admin_active_yes', 'Actief'),
            '0' => t('webshop_admin_active_no', 'Niet actief'),
        ];
    }

    protected function getProductGridPageId(): int
    {
        new Settings();
        $setting = Settings::get('page_product_grid_page_id', [
            'value' => 0,
            'label' => 'Product grid pagina',
        ]);

        return (int)($setting['value'] ?? 0);
    }

    protected function getCustomerTotals(array $orders): array
    {
        $totals = [
            'order_count' => count($orders),
            'paid_count' => 0,
            'revenue_total' => 0.0,
        ];

        foreach ($orders as $order) {
            if ((string)$order->getPaymentStatus() !== 'paid') {
                continue;
            }

            $totals['paid_count']++;
            $totals['revenue_total'] += (float)$order->getGrandTotal();
        }

        $totals['revenue_total_formatted'] = '&euro; ' . number_format($totals['revenue_total'], 2, ',', '.');

        return $totals;
    }

    protected function getWebshopSettings(): array
    {
        new Settings();

        return [
            [
                'title' => t('webshop_admin_settings_pages', 'Pagina koppelingen'),
                'fields' => [
                    'page_product_grid_page_id' => $this->settingField('page_product_grid_page_id', 'Product grid pagina', 0, 'number'),
                    'page_webshop_cart_id' => $this->settingField('page_webshop_cart_id', 'Winkelwagen pagina', 0, 'number'),
                    'page_webshop_checkout_id' => $this->settingField('page_webshop_checkout_id', 'Checkout pagina', 0, 'number'),
                    'page_webshop_favorites_id' => $this->settingField('page_webshop_favorites_id', 'Favorieten pagina', 0, 'number'),
                    'page_webshop_paymentreturn_id' => $this->settingField('page_webshop_paymentreturn_id', 'Betaalreturn pagina', 0, 'number'),
                ],
            ],
            [
                'title' => t('webshop_admin_settings_mail', 'Mail'),
                'fields' => [
                    'webshop_admin_email' => $this->settingField('webshop_admin_email', 'Admin e-mail', '', 'email'),
                    'webshop_send_order_created_mail' => $this->settingField('webshop_send_order_created_mail', 'Orderbevestiging versturen', 1, 'checkbox'),
                    'webshop_send_status_mail' => $this->settingField('webshop_send_status_mail', 'Statusmails versturen', 1, 'checkbox'),
                ],
            ],
            [
                'title' => t('webshop_admin_settings_payment', 'Betalen'),
                'fields' => [
                    'webshop_mollie_enabled' => $this->settingField('webshop_mollie_enabled', 'Mollie inschakelen', 0, 'checkbox'),
                    'webshop_default_payment_provider' => $this->settingField('webshop_default_payment_provider', 'Standaard betaalmethode', 'manual', 'select', [
                        'manual' => 'Handmatig / achteraf',
                        'mollie' => 'Mollie',
                    ]),
                ],
            ],
            [
                'title' => t('webshop_admin_settings_ai', 'AI product generatie'),
                'fields' => [
                    'webshop_ai_desired_margin_percentage' => $this->settingField('webshop_ai_desired_margin_percentage', 'Gewenste marge (%)', 30, 'number'),
                ],
            ],
            [
                'title' => t('webshop_admin_settings_invoice', 'Facturen'),
                'fields' => [
                    'webshop_invoice_prefix' => $this->settingField('webshop_invoice_prefix', 'Factuurprefix', 'F', 'text'),
                    'webshop_invoice_pdf_auto_generate' => $this->settingField('webshop_invoice_pdf_auto_generate', 'PDF automatisch genereren', 1, 'checkbox'),
                ],
            ],
        ];
    }

    protected function settingField(string $key, string $label, $default, string $type = 'text', array $options = []): array
    {
        $setting = Settings::get($key, [
            'value' => $default,
            'label' => $label,
            'input_type' => '',
        ]);

        return [
            'key' => $key,
            'label' => $setting['label'] ?? $label,
            'value' => $setting['value'] ?? $default,
            'type' => $type,
            'options' => $options,
        ];
    }

    protected function normalizeSettingValue($value, array $field)
    {
        if ($field['type'] === 'checkbox') {
            return (int)!empty($value);
        }

        if ($field['type'] === 'number') {
            return max(0, (int)$value);
        }

        if ($field['type'] === 'select') {
            return isset($field['options'][(string)$value]) ? (string)$value : (string)$field['value'];
        }

        return trim((string)$value);
    }

    protected function getWebshopDesiredMarginPercentage(): float
    {
        new Settings();
        $setting = Settings::get('webshop_ai_desired_margin_percentage', [
            'value' => 30,
            'label' => 'Gewenste marge (%)',
        ]);

        return max(0, (float)str_replace(',', '.', (string)($setting['value'] ?? 30)));
    }

    protected function normalizeMoney(string $value): float
    {
        $value = str_replace(' ', '', trim($value));
        if ($value === '') {
            return 0.0;
        }

        $lastComma = strrpos($value, ',');
        $lastDot = strrpos($value, '.');

        if ($lastComma !== false && $lastDot !== false) {
            if ($lastComma > $lastDot) {
                $value = str_replace('.', '', $value);
                $value = str_replace(',', '.', $value);
            } else {
                $value = str_replace(',', '', $value);
            }
        } elseif ($lastComma !== false) {
            $value = str_replace(',', '.', $value);
        }

        return max(0, (float)$value);
    }

    protected function saveMoodboardItems(int $moodboardId): void
    {
        if ($moodboardId <= 0) {
            return;
        }

        $request = new Request('items');
        $rows = $request->getAll();
        if (!is_array($rows)) {
            return;
        }

        $repository = new WebshopMoodboardItemRepository();
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }

            $id = (int)($row['id'] ?? 0);
            $productId = (int)($row['product_id'] ?? 0);
            $delete = (int)($row['delete'] ?? 0) === 1;

            if ($delete && $id > 0) {
                $repository->delete($id);
                continue;
            }

            if ($productId <= 0) {
                continue;
            }

            $item = $id > 0 ? $repository->findById($id) : new WebshopMoodboardItem();
            if ($id > 0 && (!$item || (int)$item->getId() <= 0)) {
                continue;
            }

            $item
                ->setMoodboardId($moodboardId)
                ->setProductId($productId)
                ->setPositionX($row['position_x'] ?? 50)
                ->setPositionY($row['position_y'] ?? 50)
                ->setOrder((int)($row['order'] ?? 0));

            $repository->add($item);
        }
    }
}
