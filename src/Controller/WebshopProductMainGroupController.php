<?php

namespace Flexgrid\Modules\Webshop\Controller;

use Flexgrid\App\Routing\Routing;
use Flexgrid\Autowire\Definition\TemplateDefinition;
use Flexgrid\Autowire\Registry\TemplateRegistry;
use Flexgrid\Controller\ModuleController;
use Flexgrid\Modules\Webshop\Entity\WebshopProductMainGroup;
use Flexgrid\Modules\Webshop\Repository\WebshopProductGroupRepository;
use Flexgrid\Modules\Webshop\Repository\WebshopProductMainGroupRepository;
use Flexgrid\Modules\Webshop\Repository\WebshopProductRepository;
use Flexgrid\Response\TemplateResponse;

/**
 * @FG\Controller [name=WebshopProductMainGroup,type=Webshop, icon=fas fa-sitemap]
 */
class WebshopProductMainGroupController extends ModuleController
{
    /**
     * @FG\Template [name=Product hoofdgroep grid, icon=fas fa-sitemap, html={<div data-type='plugin'><h5>Product hoofdgroepen</h5></div>}]
     * @param int $pageId [name=Product hoofdgroep pagina,type=page]
     * @param int $limit [name=Aantal,type=int]
     * @param int $cardWidth [name=Kaart breedte,type=int]
     * @param string $card [name=kaart,type=Template,default=ProductMainGroupCard]
     */
    public function productMainGroupGrid($pageId = 0, $limit = 99, $cardWidth = 4, $card = 'ProductMainGroupCard')
    {
        $cardFile = $this->getTemplate('Cards', false, $card);
        $cardFile = $this->getModuleTemplate('Cards/' . $card . '.php', $cardFile);
        $limit = (int)$limit;

        return new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/ProductMainGroupGrid/ProductMainGroupGrid.php', [
            'entities' => array_slice((new WebshopProductMainGroupRepository())->getActive(), 0, $limit > 0 ? $limit : 99),
            'pageId' => (int)$pageId,
            'limit' => $limit,
            'cardWidth' => (int)$cardWidth ?: 4,
            'card' => $cardFile,
            'parentWidth' => 12,
        ]);
    }

    /**
     * @FG\Template [name=Product hoofdgroep menu, icon=fas fa-sitemap, html={<div data-type='plugin'><h5>Product hoofdgroepen</h5></div>}]
     * @param int $limit [name=Aantal hoofdgroepen,type=int]
     * @param int $pageId [name=Product overzicht pagina,type=page]
     * @param int $groupLimit [name=Aantal subgroepen,type=int]
     */
    public function productMainGroupMenu($limit = 99, $pageId = 0, $groupLimit = 99)
    {
        return new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/ProductMainGroupMenu/ProductMainGroupMenu.php', [
            'entities' => (new WebshopProductMainGroupRepository())->getActive(),
            'productGroupRepository' => new WebshopProductGroupRepository(),
            'pageId' => (int)$pageId,
            'limit' => (int)$limit,
            'groupLimit' => (int)$groupLimit,
        ]);
    }

    /**
     * @FG\Template [name=Product hoofdgroep banner, icon=fas fa-image, html={<div data-type='plugin'><h5>Product hoofdgroep banner</h5></div>}]
     * @param int $mainGroupId [name=Hoofdgroep,type=group]
     * @param string $buttonText [name=Knoptekst,type=text]
     * @param int $productGroupPageId [name=Productgroep pagina,type=page]
     */
    public function banner($mainGroupId = 0, $buttonText = 'Ontdek de collectie', $productGroupPageId = 0)
    {
        return new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/ProductMainGroupLanding/Banner.php', [
            'entity' => $this->getMainGroup((int)$mainGroupId),
            'buttonText' => $buttonText,
            'productGroupPageId' => (int)$productGroupPageId,
        ]);
    }

    /**
     * @FG\Template [name=Product hoofdgroep productgroepen, icon=fas fa-layer-group, html={<div data-type='plugin'><h5>Productgroepen binnen hoofdgroep</h5></div>}]
     * @param int $mainGroupId [name=Hoofdgroep,type=group]
     * @param int $pageId [name=Product overzicht pagina,type=page]
     * @param int $limit [name=Aantal,type=int]
     * @param int $cardWidth [name=Kaart breedte,type=int]
     * @param string $card [name=kaart,type=Template,default=ProductGroupCard2]
     */
    public function productGroups($mainGroupId = 0, $pageId = 0, $limit = 99, $cardWidth = 3, $card = 'ProductGroupCard2')
    {
        $mainGroup = $this->getMainGroup((int)$mainGroupId);

        return new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/ProductMainGroupLanding/ProductGroups.php', [
            'entity' => $mainGroup,
            'productGroupGrid' => (new WebshopProductGroupController())->productGroupGrid(
                (int)$pageId,
                $mainGroup ? (int)$mainGroup->getId() : 0,
                (int)$limit,
                (int)$cardWidth,
                $card
            ),
        ]);
    }

    /**
     * @FG\Template [name=Product hoofdgroep CTA, icon=fas fa-bullhorn, html={<div data-type='plugin'><h5>Product hoofdgroep CTA</h5></div>}]
     * @param int $mainGroupId [name=Hoofdgroep,type=group]
     * @param string $buttonText [name=Knoptekst,type=text]
     * @param string $buttonUrl [name=Knop URL,type=text]
     */
    public function cta($mainGroupId = 0, $buttonText = '', $buttonUrl = '')
    {
        return new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/ProductMainGroupLanding/Cta.php', [
            'entity' => $this->getMainGroup((int)$mainGroupId),
            'buttonText' => $buttonText,
            'buttonUrl' => $buttonUrl,
        ]);
    }

    /**
     * @FG\Template [name=Product hoofdgroep slider, icon=fas fa-sliders-h, html={<div data-type='plugin'><h5>Product hoofdgroep slider</h5></div>}]
     * @param int $mainGroupId [name=Hoofdgroep,type=group]
     * @param int $pageId [name=Detail pagina,type=page]
     * @param string $mode [name=Selectie,type=select,options={new::Nieuw,highlight::Highlight,all::Alles}]
     * @param int $limit [name=Aantal,type=int]
     * @param string $card [name=kaart,type=Template,default=ProductCard2]
     * @param int $cardWidth [name=Kaart breedte,type=int]
     */
    public function slider($mainGroupId = 0, $pageId = 0, $mode = 'new', $limit = 4, $card = 'ProductCard2', $cardWidth = 3)
    {
        $mainGroup = $this->getMainGroup((int)$mainGroupId);
        $products = $mainGroup ? (new WebshopProductRepository())->getByMainGroupId((int)$mainGroup->getId(), 99) : [];

        if ($mode === 'highlight') {
            $products = array_values(array_filter($products, static function ($product) {
                return trim((string)$product->getHighlightText()) !== '';
            }));
        } elseif ($mode === 'new') {
            usort($products, static function ($left, $right) {
                return strtotime((string)$right->getMakeTime()) <=> strtotime((string)$left->getMakeTime());
            });
        }

        $cardFile = (new WebshopProductController())->getTemplate('Cards', false, $card);
        if (is_file('Flexgrid/Modules/Webshop/src/Templates/Cards/' . $card . '.php')) {
            $cardFile = 'Flexgrid/Modules/Webshop/src/Templates/Cards/' . $card . '.php';
        }

        return new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/ProductMainGroupLanding/Slider.php', [
            'entity' => $mainGroup,
            'entities' => array_slice($products, 0, (int)$limit),
            'card' => $cardFile,
            'pageId' => (int)$pageId,
            'cardWidth' => (int)$cardWidth ?: 3,
            'parentWidth' => 12,
            'mode' => $mode,
        ]);
    }

    /**
     * @FG\Template [name=Product hoofdgroep highlight, icon=fas fa-star, html={<div data-type='plugin'><h5>Product hoofdgroep highlight</h5></div>}]
     * @param int $mainGroupId [name=Hoofdgroep,type=group]
     * @param string $buttonText [name=Knoptekst,type=text]
     * @param string $buttonUrl [name=Knop URL,type=text]
     */
    public function highlight($mainGroupId = 0, $buttonText = '', $buttonUrl = '')
    {
        return new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/ProductMainGroupLanding/Highlight.php', [
            'entity' => $this->getMainGroup((int)$mainGroupId),
            'buttonText' => $buttonText,
            'buttonUrl' => $buttonUrl,
        ]);
    }

    protected function getMainGroup(int $mainGroupId = 0)
    {
        if ($mainGroupId > 0) {
            return (new WebshopProductMainGroupRepository())->findById($mainGroupId);
        }

        $current = Routing::currentEntity();
        if ($current instanceof WebshopProductMainGroup) {
            return $current;
        }

        return null;
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
