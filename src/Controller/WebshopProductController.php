<?php

namespace Flexgrid\Modules\Webshop\Controller;

use App\Filters\QueryBuilder;
use Flexgrid\App\Routing\Routing;
use Flexgrid\Autowire\Definition\TemplateDefinition;
use Flexgrid\Autowire\Registry\TemplateRegistry;
use Flexgrid\Controller\ModuleController;
use Flexgrid\Modules\Webshop\Repository\WebshopProductGroupRepository;
use Flexgrid\Modules\Webshop\Repository\WebshopProductRepository;
use Flexgrid\Modules\Webshop\Service\CartService;
use Flexgrid\Modules\Webshop\Service\RoutingService;
use Flexgrid\Response\AjaxResponse;
use Flexgrid\Response\TemplateResponse;
use Flexgrid\Utils\Request\Request;

/**
 * @FG\Controller [name=WebshopProduct,type=Webshop, icon=fas fa-box]
 */
class WebshopProductController extends ModuleController
{
    protected $filterAjaxTargetController = null;

    /**
     * @FG\Template [name=Product grid, icon=fas fa-th, html={<div data-type='plugin'><h5>Product grid</h5></div>},create_override=true]
     * @param int $pageId [name=Detail pagina,type=page]
     * @param string $template [name=template,type=Template]
     * @param string $card [name=kaart,type=Template,default=ProductCard]
     * @param int $mainGroupId [name=Hoofdgroep,type=group]
     * @param int $groupId [name=Productgroep,type=group]
     * @param int $limit [name=Aantal,type=int]
     * @param int $cardWidth [name=Kaart breedte,type=int]
     * @param string $color [name=Kleur,type=text]
     * @param string $size [name=Maat,type=text]
     */
    public function productGrid($pageId = 0, $template = 'ProductGrid', $card = 'ProductCard', $mainGroupId = 0, $groupId = 0, $limit = 12, $cardWidth = 4, $color = '', $size = '')
    {
        $repository = $this->getRepository();
        $url = Routing::currentUrl();

        if ($url !== null && ($_SERVER['in_detail'] ?? false) != true) {
            $entity = RoutingService::getCurrentWebshopProduct();
            if( $entity != false) {

                return $this->renderProductDetail((int)$url->getEntityId(), (int)$pageId);

            }
        }

        if ((int)$groupId === 0) {
            $current = RoutingService::getCurrentWebshopGroup();
            if (is_object($current) ) {
                $groupId = $current->getId();
            }
        }

        $currentGroup = null;
        if ((int)$groupId > 0) {
            $currentGroup = (new WebshopProductGroupRepository())->findById((int)$groupId);
        }

        $templateFile = $this->getModuleTemplate($template . '/' . $template . '.php', $this->getTemplate($template));
        $cardFile = $this->getTemplate('Cards', false, $card);
        $cardFile = $this->getModuleTemplate('Cards/' . $card . '.php', $cardFile);

        $color = trim((string)$color);
        $size = trim((string)$size);

        $baseConstraints = $this->getProductGridFilterConstraints((int)$mainGroupId, (int)$groupId, $color, $size);
        $filterData = $this->getFilters(
            null,
            null,
            $baseConstraints
        );
        $filterQuery = $this->getProductGridFilterQuery($filterData, $baseConstraints);
        $order = $this->resolveProductGridOrder($repository);
        $pagination = $this->getProductGridPagination((int)$limit, $filterQuery, $order);


        if ($filterQuery !== '') {

            $products = $repository->getAll(
                max((int)$limit * 3, (int)$limit), // waarom is dit?!
                null,
                $filterQuery,
                false,
                $order
            );
            $entities = array_slice($repository->filterVariantClusters($products), 0, (int)$limit);
        } elseif ($color !== '' || $size !== '') {
            $filters = [
                'group_id' => (int)$groupId,
                'main_group_id' => (int)$mainGroupId,
                'status' => 'published',
                'active' => '1',
                'color' => $color,
                'size' => $size,
            ];
            $entities = array_slice($repository->filterVariantClusters($repository->search($filters, max((int)$limit * 3, (int)$limit), $order)), 0, (int)$limit);
        } elseif ((int)$groupId > 0) {
            $entities = $repository->getByGroupId((int)$groupId, (int)$limit, $order);
        } elseif ((int)$mainGroupId > 0) {
            $entities = $repository->getByMainGroupId((int)$mainGroupId, (int)$limit, $order);
        } else {
            $entities = $repository->getActive((int)$limit, $order);
        }

        $response =  new TemplateResponse($templateFile, [
            'entities' => $entities,
            'card' => $cardFile,
            'pageId' => (int)$pageId,
            'cardWidth' => (int)$cardWidth ?: 4,
            'parentWidth' => 12,
            'currentGroup' => $currentGroup,
            'productCount' => count($entities),
            'pagination' => $pagination,
        ]);

        if($this->request->get('ajax') == 'true' || (defined('__AJAX__') && __AJAX__ === true)){
            $ajaxResponse = new AjaxResponse();
            $ajaxTargetController = $this->filterAjaxTargetController ?: get_class($this);
            $paginationHtml = (string)$this->renderProductPagination($pagination, $ajaxTargetController);
            $ajaxResponse->setContainer($this->getProductGridSelector($ajaxTargetController),(string)$response);
            $ajaxResponse->setContainer(
                $this->getProductPaginationSelector($ajaxTargetController),
                $paginationHtml,
                true
            );

            if ($ajaxTargetController !== get_class($this)) {
                $ajaxResponse->setContainer(
                    $this->getProductPaginationSelector(get_class($this)),
                    (string)$this->renderProductPagination($pagination, get_class($this)),
                    true
                );
            }

            return $ajaxResponse;
        }
        return $response;
    }

    /**
     * @FG\Template [name=Product pagination, icon=fas fa-ellipsis-h, html={<div data-type='plugin'><h5>Product pagination</h5></div>}]
     * @param int $mainGroupId [name=Hoofdgroep,type=group]
     * @param int $groupId [name=Productgroep,type=group]
     * @param int $limit [name=Aantal,type=int]
     * @param string $color [name=Kleur,type=text]
     * @param string $size [name=Maat,type=text]
     */
    public function productPagination($mainGroupId = 0, $groupId = 0, $limit = 12, $color = '', $size = '')
    {
        $repository = $this->getRepository();
        $baseConstraints = $this->getProductGridFilterConstraints((int)$mainGroupId, (int)$groupId, $color, $size);
        $filterData = $this->getFilters(
            null,
            null,
            $baseConstraints
        );
        $filterQuery = $this->getProductGridFilterQuery($filterData, $baseConstraints);
        $order = $this->resolveProductGridOrder($repository);
        $pagination = $this->getProductGridPagination((int)$limit, $filterQuery, $order);
        $ajaxTargetController = $this->filterAjaxTargetController ?: get_class($this);

        return $this->renderProductPagination($pagination, $ajaxTargetController);
    }

    /**
     * @FG\Template [name=Uitgelichte producten, icon=fas fa-star, html={<div data-type='plugin'><h5>Uitgelichte producten</h5></div>}]
     * @param int $pageId [name=Detail pagina,type=page]
     * @param string $template [name=template,type=Template]
     * @param string $card [name=kaart,type=Template,default=ProductCard]
     * @param int $limit [name=Aantal,type=int]
     * @param int $cardWidth [name=Kaart breedte,type=int]
     */
    public function featuredProducts($pageId = 0, $template = 'ProductGrid', $card = 'ProductCard', $limit = 4, $cardWidth = 3)
    {
        return $this->productGrid($pageId, $template, $card, 0, 0, $limit, $cardWidth);
    }

    public function getRepository()
    {
        if($this->repository == null)
        {
            $this->repository = new WebshopProductRepository();
        }
        return $this->repository ;
    }

    public function setFilterAjaxTargetController(string $controllerClass)
    {
        $this->filterAjaxTargetController = $controllerClass;
        return $this;
    }

    public function getFilterClass($groupId = null, $where = null, array $extraConstraints = [])
    {
        return parent::getFilterClass(
            null,
            null,
            array_merge($extraConstraints, $this->getProductGridFilterConstraints(0, (int)$groupId))
        );
    }

    protected function resolveProductGridOrder(WebshopProductRepository $repository): string
    {
        $entityName = getClassName($repository->getNew());
        $requested = $_REQUEST['sortorder'][$entityName] ?? '';
        $requested = is_string($requested) ? trim($requested) : '';
        $sortOptions = $repository->getSortOptions();

        if ($requested !== '' && isset($sortOptions[$requested])) {
            return $requested;
        }

        return 'title:ASC';
    }

    protected function getProductGridPagination(int $limit, string $filterQuery, string $order): array
    {
        $pagination = $this->getRepository()
            ->setPagination(true)
            ->getPagination($limit, $filterQuery);

        if (!is_array($pagination)) {
            return [];
        }

        $pagination['order'] = $order;
        $pagination['limit'] = $limit;

        return $pagination;
    }

    protected function getProductGridFilterQuery(array $filterData, array $baseConstraints): string
    {
        $query = trim((string)($filterData['query'] ?? ''));
        if ($query !== '') {
            return $query;
        }

        return $this->buildConstraintQuery($baseConstraints);
    }

    protected function buildConstraintQuery(array $constraints): string
    {
        $queryBuilder = new QueryBuilder();

        foreach ($constraints as $constraint) {
            if (empty($constraint['sql'])) {
                continue;
            }

            $queryBuilder->where($constraint['sql']);
            foreach ((array)($constraint['params'] ?? []) as $key => $value) {
                $queryBuilder->bind($key, $value);
            }
        }

        return trim((string)$queryBuilder->getWhere());
    }

    protected function renderProductPagination(array $pagination, string $ajaxTargetController)
    {
        return new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/ProductGrid/Pagination/Pagination.php', [
            'pagination' => $pagination,
            'target' => md5($ajaxTargetController),
        ]);
    }

    protected function getProductGridSelector(string $ajaxTargetController): string
    {
        return '[data-filter-result="' . md5($ajaxTargetController) . '"]';
    }

    protected function getProductPaginationSelector(string $ajaxTargetController): string
    {
        return '[data-webshop-product-pagination="' . md5($ajaxTargetController) . '"]';
    }

    protected function getProductGridFilterConstraints(int $mainGroupId = 0, int $groupId = 0, $color = '', $size = ''): array
    {
        $color = trim((string)$color);
        $size = trim((string)$size);

        if ($groupId === 0) {
            $current = RoutingService::getCurrentWebshopGroup();
            if (is_object($current)) {
                $groupId = (int)$current->getId();
            }
        }

        $constraints = [
            [
                'sql' => 'is_hidden = :is_hidden || is_hidden IS NULL',
                'params' => [
                    ':is_hidden' => 0,
                ],
            ]
        ];

        if ($groupId > 0) {
            $constraints[] = [
                'sql' => 'group_id = :product_group_id',
                'params' => [
                    ':product_group_id' => $groupId,
                ],
            ];
        } elseif ($mainGroupId > 0) {
            $groupIds = (new WebshopProductGroupRepository())->getIdsByMainGroupId($mainGroupId);
            if (empty($groupIds)) {
                $constraints[] = [
                    'sql' => '0 = 1',
                    'params' => [],
                ];

                return $constraints;
            }

            $params = [];
            $placeholders = [];
            foreach (array_values($groupIds) as $index => $id) {
                $placeholder = ':product_main_group_' . $index;
                $placeholders[] = $placeholder;
                $params[$placeholder] = (int)$id;
            }

            $constraints[] = [
                'sql' => 'group_id IN (' . implode(', ', $placeholders) . ')',
                'params' => $params,
            ];
        }

        if ($color !== '') {
            $constraints[] = [
                'sql' => 'color = :product_color',
                'params' => [
                    ':product_color' => $color,
                ],
            ];
        }

        if ($size !== '') {
            $constraints[] = [
                'sql' => 'size = :product_size',
                'params' => [
                    ':product_size' => $size,
                ],
            ];
        }

        return $constraints;
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
        $response->setContainer('.webshop-cart-summary', (string)(new WebshopController())->cartSummary(), true);
        $response->setContainer('.js-webshop-cart-count', (string)($result['cart']['quantity'] ?? 0));

        return $response;
    }

    protected function renderProductDetail(int $productId, int $pageId = 0)
    {
        $product = $this->getRepository()->findById($productId);
        if (!$product || (int)$product->getId() <= 0) {
            return '';
        }

        $relatedProducts = [];
        if ((int)$product->getGroupId() > 0) {
            foreach ($this->getRepository()->getByGroupId((int)$product->getGroupId(), 5) as $relatedProduct) {
                if (!$relatedProduct || (int)$relatedProduct->getId() === (int)$product->getId()) {
                    continue;
                }

                $relatedProducts[] = $relatedProduct;
            }
        }

        $_SERVER['in_detail'] = true;

        return new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/ProductGrid/ProductGridDetail.php', [
            'entity' => $product,
            'controller' => $this,
            'pageId' => $pageId,
            'relatedProducts' => array_slice($relatedProducts, 0, 4),
        ]);
    }

    protected function getModuleTemplate(string $relativePath, string $fallback): string
    {
        $appPath = 'App/Webshop/Templates/' . ltrim($relativePath, '/');
        if (is_file($appPath)) {
            return $appPath;
        }

        $path = 'Flexgrid/Modules/Webshop/src/Templates/' . ltrim($relativePath, '/');

        if (is_file($path)) {
            return $path;
        }

        return $fallback;
    }

    protected function renderCartFeedback(bool $success, string $message): string
    {
        $class = $success ? 'webshop-cart-feedback__message webshop-cart-feedback__message--success' : 'webshop-cart-feedback__message webshop-cart-feedback__message--error';

        return '<div class="' . $class . '">' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</div>';
    }

    public function getTemplate($name, $detail = false, $file = null)
    {
        if ($name == null) {
            return null;
        }

        $templateDefintion = TemplateRegistry::getTemplateByControllerAndMethod(get_class($this), $name);

        /**
         * @var TemplateDefinition $templateDefintion
         */
        if (empty($templateDefintion)) {
            return parent::getTemplate($name, $detail, $file);
        }

        if (is_object($templateDefintion)) {
            return path($templateDefintion->getPath());
        }

        return '';
    }
}
