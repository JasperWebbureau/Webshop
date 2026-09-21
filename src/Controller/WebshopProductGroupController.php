<?php

namespace Flexgrid\Modules\Webshop\Controller;

use Flexgrid\App\Routing\Routing;
use Flexgrid\Autowire\Definition\TemplateDefinition;
use Flexgrid\Autowire\Registry\TemplateRegistry;
use Flexgrid\Controller\ModuleController;
use Flexgrid\Modules\Webshop\Repository\WebshopProductGroupRepository;
use Flexgrid\Modules\Webshop\Service\RoutingService;
use Flexgrid\Response\TemplateResponse;

/**
 * @FG\Controller [name=WebshopProductGroup,type=Webshop, icon=fas fa-layer-group]
 */
class WebshopProductGroupController extends ModuleController
{
    /**
     * @FG\Template [name=Productgroep hero / intro, icon=fas fa-layer-group, html={<div data-type='plugin'><h5>Product groep hero</h5></div>}]
     */
    public function groupHero($groupId = 0)
    {
        $currentGroup = null;
        $current = RoutingService::getCurrentWebshopGroup();
        if( $current !== false){
            $groupId = $current->getId();
            $currentGroup = $current;

        }

        if ((int)$groupId > 0) {
            $currentGroup = (new WebshopProductGroupRepository())->findById((int)$groupId);
        }

        return new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/GroupHero/GroupHero.php', [ 'currentGroup' => $currentGroup,
            'productCount' => count([])]);
    }
    /**
     * @FG\Template [name=Productgroep grid, icon=fas fa-layer-group, html={<div data-type='plugin'><h5>Productgroepen</h5></div>}]
     * @param int $pageId [name=Product overzicht pagina,type=page]
     * @param int $mainGroupId [name=Hoofdgroep,type=group]
     * @param int $limit [name=Aantal,type=int]
     * @param int $cardWidth [name=Kaart breedte,type=int]
     * @param string $card [name=kaart,type=Template,default=ProductGroupCard]
     */
    public function productGroupGrid($pageId = 0, $mainGroupId = 0, $limit = 99, $cardWidth = 4, $card = 'ProductGroupCard')
    {
        if ((int)$mainGroupId === 0) {
            $current = Routing::currentEntity();
            if (is_object($current) && get_class($current) === 'Flexgrid\Modules\Webshop\Entity\WebshopProductMainGroup') {
                $mainGroupId = $current->getId();
            }
        }

        $cardFile = $this->getTemplate('Cards', false, $card);
        $cardFile = $this->getModuleTemplate('Cards/' . $card . '.php', $cardFile);
        $repository = new WebshopProductGroupRepository();

        return new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/ProductGroupGrid/ProductGroupGrid.php', [
            'entities' => (int)$mainGroupId > 0 ? $repository->getByMainGroupId((int)$mainGroupId, (int)$limit) : $repository->getAll($limit),
            'pageId' => (int)$pageId,
            'limit' => (int)$limit,
            'cardWidth' => (int)$cardWidth ?: 4,
            'card' => $cardFile,
            'parentWidth' => 12,
        ]);
    }

    /**
     * @FG\Template [name=Productgroep menu, icon=fas fa-list, html={<div data-type='plugin'><h5>Productgroep menu</h5></div>}]
     * @param int $limit [name=Aantal,type=int]
     * @param int $pageId [name=Product overzicht pagina,type=page]
     * @param int $mainGroupId [name=Hoofdgroep,type=group]
     */
    public function productGroupMenu($limit = 99, $pageId = 0, $mainGroupId = 0)
    {
        $repository = new WebshopProductGroupRepository();

        return new TemplateResponse('Flexgrid/Modules/Webshop/src/Templates/ProductGroupMenu/ProductGroupMenu.php', [
            'entities' => (int)$mainGroupId > 0 ? $repository->getByMainGroupId((int)$mainGroupId, (int)$limit) : $repository->getActive(),
            'pageId' => (int)$pageId,
            'limit' => (int)$limit,
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
