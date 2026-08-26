<?php

namespace Flexgrid\Modules\Webshop\Entity;

use Flexgrid\Autowire\Relation\SiblingRelationManager;
use Flexgrid\Utils\Files\ImageFile;
use Repository\ModuleEntity;

/**
 * @FG\Entity[name=WebshopProduct,repository=Flexgrid\Modules\Webshop\Repository\WebshopProductRepository,type=Webshop,in_menu=true,defaultOrder=title:ASC]
 */
class WebshopProduct extends ModuleEntity
{
    /**
     * @FG\Column[type=primary]
     * @FG\listForm[0=]
     */
    protected $id;

    /**
     * @FG\Column[type=varchar,fill=title,roles=share_title|title,search=true,sortoption={asc:A-z,desc:Z-a}]
     * @FG\Filter::default[type=textsearch,html_title=Zoeken,fields={title,sku,shortDescription,color,size,manufacturer}]
     * @FG\listForm[width=3]
     */
    protected $title;

    /**
     * @FG\Column[type=varchar,length=80,search=true,label=SKU]
     * @FG\listForm[width=2]
     */
    protected $sku;

    /**
     * @FG\Column[type=int,parent=Flexgrid\Modules\Webshop\Entity\WebshopProductGroup::id,role=group]
     * @FG\Filter::default[type=checkboxMultipleSelect,html_title=Productgroep]
     * @FG\listForm[ignore=true]
     */
    protected $groupId;

    /**
     * @FG\Sibling[target=Flexgrid\Modules\Webshop\Entity\WebshopProduct,useGroups=true]
     * @FG\Flexgrid\Modules\Webshop\Entity\WebshopProduct_edit[width=12,group=Varianten,label=Gekoppelde producten]
     */
    protected $linkedProducts;

    /**
     * @FG\Column[type=image,roles=share_image|main_image]
     * @FG\listForm[ignore=true]
     */
    protected $image;

    /**
     * @FG\Column[type=images]
     * @FG\listForm[ignore=true]
     */
    protected $images;

    /**
     * @FG\Column[type=textarea,label=Korte omschrijving]
     * @FG\listForm[ignore=true]
     */
    protected $shortDescription;

    /**
     * @FG\Column[type=html,label=Omschrijving]
     * @FG\listForm[ignore=true]
     */
    protected $description;

    /**
     * @FG\Column[type=image,label=Highlight afbeelding]
     * @FG\listForm[ignore=true]
     */
    protected $highlightImage;

    /**
     * @FG\Column[type=html,label=Highlight tekst]
     * @FG\listForm[ignore=true]
     */
    protected $highlightText;

    /**
     * @FG\Column[type=monetary,label=Prijs]
     * @FG\listForm[width=2]
     */
    protected $price;

    /**
     * @FG\Column[type=monetary,label=Inkoopprijs]
     * @FG\listForm[ignore=true]
     */
    protected $purchasePrice;

    /**
     * @FG\Column[type=monetary,label=Actieprijs]
     * @FG\listForm[ignore=true]
     */
    protected $salePrice;

    /**
     * @FG\Column[type=float,label=BTW percentage]
     * @FG\listForm[ignore=true]
     */
    protected $taxRate;

    /**
     * @FG\Column[type=int,parent=Flexgrid\Modules\Webshop\Entity\WebshopTaxRate::id,label=BTW tarief]
     * @FG\listForm[ignore=true]
     */
    protected $taxRateId;

    /**
     * @FG\Column[type=int,label=Voorraad]
     * @FG\listForm[width=1]
     */
    protected $stock;

    /**
     * @FG\Column[type=tinyint,label=Voorraad bijhouden]
     * @FG\listForm[ignore=true]
     */
    protected $trackStock;



    /**
     * @FG\Column[type=varchar,label=Kleur,search=true]
     * @FG\Filter::default[type=color,html_title=Kleur]
     * @FG\listForm[width=2]
     */
    protected $color;

    /**
     * @FG\Column[type=varchar,label=Maat,search=true]
     * @FG\Filter::default[type=checkboxMultipleSelect,html_title=Maat]
     * @FG\listForm[width=2]
     */
    protected $size;

    /**
     * @FG\Column[type=varchar,label=Fabrikant/merk,search=true]
     * @FG\listForm[width=2]
     */
    protected $manufacturer;


    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($value)
    {
        $this->title = $value;
        return $this;
    }

    public function getSku()
    {
        return $this->sku;
    }

    public function setSku($value)
    {
        $this->sku = $value;
        return $this;
    }

    public function getGroupId()
    {
        return $this->groupId;
    }

    public function setGroupId($value)
    {
        $this->groupId = $value;
        return $this;
    }

    public function getImage()
    {
        return ImageFile::getImageFile($this->image);
    }

    public function setImage($value)
    {
        $this->image = $value;
        return $this;
    }

    public function getImages()
    {
        return $this->images;
    }

    public function setImages($value)
    {
        $this->images = $value;
        return $this;
    }

    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    public function setShortDescription($value)
    {
        $this->shortDescription = $value;
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($value)
    {
        $this->description = $value;
        return $this;
    }

    public function getHighlightImage()
    {
        return ImageFile::getImageFile($this->highlightImage);
    }

    public function setHighlightImage($value)
    {
        $this->highlightImage = $value;
        return $this;
    }

    public function getHighlightText()
    {
        return $this->highlightText;
    }

    public function setHighlightText($value)
    {
        $this->highlightText = $value;
        return $this;
    }

    public function getPrice()
    {
        return $this->price;
    }

    
    public function getSalePrice()
    {
        return $this->salePrice;
    }

    public function getPurchasePrice()
    {
        return $this->purchasePrice;
    }

    public function setPurchasePrice($value)
    {
        $this->purchasePrice = $value;
        return $this;
    }

    public function setSalePrice($value)
    {
        $this->salePrice = $value;
        return $this;
    }

    public function getTaxRate()
    {
        return $this->taxRate;
    }

    public function setTaxRate($value)
    {
        $this->taxRate = $value;
        return $this;
    }

    public function getTaxRateId()
    {
        return (int)$this->taxRateId;
    }

    public function setTaxRateId($value)
    {
        $this->taxRateId = (int)$value;
        return $this;
    }

    public function getStock()
    {
        return $this->stock;
    }

    public function setStock($value)
    {
        $this->stock = $value;
        return $this;
    }

    public function getTrackStock()
    {
        return $this->trackStock;
    }

    public function setTrackStock($value)
    {
        $this->trackStock = $value;
        return $this;
    }

    public function getIsActive()
    {
        return 1;
        return $this->isActive;
    }

    public function setIsActive($value)
    {
        $this->isActive = $value;
        return $this;
    }

    public function getStatus()
    {
        return  'published';
        return $this->status;
    }

    public function setStatus($value)
    {
        $this->status = $value;
        return $this;
    }

    public function getColor()
    {
        return $this->color;
    }

    public function setColor($value)
    {
        $this->color = $value;
        return $this;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function setSize($value)
    {
        $this->size = $value;
        return $this;
    }

    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    public function setManufacturer($value)
    {
        $this->manufacturer = $value;
        return $this;
    }


    // --- Auto-generated getters and setters ---

   

    /**
     * Get a display value for the parent entity of groupId.
     */
    public function getGroupIdValue($getter = null)
    {
        $parent = $this->getGroupIdParent();
        if ($parent === null) {
            return null;
        }

        if (!empty($getter) && method_exists($parent, $getter)) {
            return $parent->{$getter}();
        }

        if (method_exists($parent, 'getTitle')) {
            return $parent->getTitle();
        }

        if (method_exists($parent, 'getName')) {
            return $parent->getName();
        }

        return $parent;
    }

    


    // --- Auto-generated getters and setters ---

    /**
     * Get the value of isHidden
     */
    public function getIsHidden()
    {
        return $this->isHidden;
    }

    /**
     * Set the value of isHidden
     *
     * @param mixed $value
     * @return $this
     */
    public function setIsHidden($value)
    {
        $this->isHidden = $value;
        return $this;
    }

    /**
     * Get the value of order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set the value of order
     *
     * @param mixed $value
     * @return $this
     */
    public function setOrder($value)
    {
        $this->order = $value;
        return $this;
    }

    /**
     * Get the value of makeTime
     */
    public function getMakeTime()
    {
        return $this->makeTime;
    }

    /**
     * Get the value of lastUpdate
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }

    /**
     * Set the value of lastUpdate
     *
     * @param mixed $value
     * @return $this
     */
    public function setLastUpdate($value)
    {
        $this->lastUpdate = $value;
        return $this;
    }



    // --- Auto-generated getters and setters ---

    /**
     * Set the value of price
     *
     * @param mixed $value
     * @return $this
     */
    public function setPrice($value)
    {
        $this->price = $value;
        return $this;
    }













    // --- Auto-generated getters and setters ---

    /**
     * Get the parent entity for groupId.
     */
    public function getWebshopProductGroupParent()
    {
        $id = $this->getGroupId();
        if (empty($id)) {
            return null;
        }

        if (!isset($this->__autowireParentCache['groupIdParent']) ||
            $this->__autowireParentCache['groupIdParent']['id'] != $id) {
            $parent = (new \Flexgrid\Modules\Webshop\Repository\WebshopProductGroupRepository())->select()->where('id = ?', [$id])->get()[0] ?? null;
            $this->__autowireParentCache['groupIdParent'] = [
                'id' => $id,
                'value' => $parent,
            ];
        }

        return $this->__autowireParentCache['groupIdParent']['value'];
    }

    /**
     * Get a display value for the parent entity of groupId.
     */
    public function getWebshopProductGroupValue($getter = null)
    {
        $parent = $this->getWebshopProductGroupParent();
        if ($parent === null) {
            return null;
        }

        if (!empty($getter) && method_exists($parent, $getter)) {
            return $parent->{$getter}();
        }

        if (method_exists($parent, 'getTitle')) {
            return $parent->getTitle();
        }

        if (method_exists($parent, 'getName')) {
            return $parent->getName();
        }

        return $parent;
    }



    // --- Auto-generated getters and setters ---

    /**
     * Get the children of WebshopOrderLine
     */
    public function getWebshopOrderLineChildren()
    {
        return (new \Flexgrid\Modules\Webshop\Repository\WebshopOrderLineRepository)->select()->where('`product_id` = ?',[ $this->getId()])->get();;
    }



    // --- Auto-generated getters and setters ---

    /**
     * Get the children of WebshopStockMutation
     */
    public function getWebshopStockMutationChildren()
    {
        return (new \Flexgrid\Modules\Webshop\Repository\WebshopStockMutationRepository)->select()->where('`product_id` = ?',[ $this->getId()])->get();;
    }



    // --- Auto-generated getters and setters ---

    /**
     * Get the parent entity for taxRateId.
     */
    public function getWebshopTaxRateParent()
    {
        $id = $this->getTaxRateId();
        if (empty($id)) {
            return null;
        }

        if (!isset($this->__autowireParentCache['taxRateIdParent']) ||
            $this->__autowireParentCache['taxRateIdParent']['id'] != $id) {
            $parent = (new \Flexgrid\Modules\Webshop\Repository\WebshopTaxRateRepository())->select()->where('id = ?', [$id])->get()[0] ?? null;
            $this->__autowireParentCache['taxRateIdParent'] = [
                'id' => $id,
                'value' => $parent,
            ];
        }

        return $this->__autowireParentCache['taxRateIdParent']['value'];
    }

    /**
     * Get a display value for the parent entity of taxRateId.
     */
    public function getWebshopTaxRateValue($getter = null)
    {
        $parent = $this->getWebshopTaxRateParent();
        if ($parent === null) {
            return null;
        }

        if (!empty($getter) && method_exists($parent, $getter)) {
            return $parent->{$getter}();
        }

        if (method_exists($parent, 'getTitle')) {
            return $parent->getTitle();
        }

        if (method_exists($parent, 'getName')) {
            return $parent->getName();
        }

        return $parent;
    }

    /**
     * Get the sibling ids for linkedProducts.
     */
    public function getLinkedProducts()
    {
        return SiblingRelationManager::getCsv(get_class($this), 'linkedProducts', $this->getId(), 'Flexgrid\Modules\Webshop\Entity\WebshopProduct');
    }

    /**
     * Get the sibling entities for linkedProducts.
     */
    public function getLinkedProductsEntities()
    {
        return SiblingRelationManager::getEntities(get_class($this), 'linkedProducts', $this->getId(), 'Flexgrid\Modules\Webshop\Entity\WebshopProduct');
    }

    public function getVariantClusterProducts(bool $onlyAvailable = false): array
    {
        $products = [(int)$this->getId() => $this];

        foreach ($this->getLinkedProductsEntities() as $product) {
            if (!$product || (int)$product->getId() <= 0) {
                continue;
            }

            if ($onlyAvailable && (
                (int)$product->getIsActive() !== 1 ||
                (string)$product->getStatus() !== 'published'
            )) {
                continue;
            }

            $products[(int)$product->getId()] = $product;
        }

        if ($onlyAvailable && (
            (int)$this->getIsActive() !== 1 ||
            (string)$this->getStatus() !== 'published'
        )) {
            unset($products[(int)$this->getId()]);
        }

        uasort($products, static function ($a, $b) {
            return strcmp((string)$a->getTitle(), (string)$b->getTitle());
        });

        return array_values($products);
    }

    public function getVariantClusterKey(): string
    {
        $ids = [(int)$this->getId()];

        foreach ($this->getLinkedProductsEntities() as $product) {
            if ($product && (int)$product->getId() > 0) {
                $ids[] = (int)$product->getId();
            }
        }

        $ids = array_values(array_unique($ids));
        sort($ids);

        return implode('-', $ids);
    }



    // --- Auto-generated getters and setters ---

    /**
     * Set the value of linkedProducts
     *
     * @param mixed $value
     * @return $this
     */
    public function setLinkedProducts($value)
    {
        $this->linkedProducts = $value;
        return $this;
    }



    // --- Auto-generated getters and setters ---

    /**
     * Get the children of Review
     */
    public function getReviewChildren()
    {
        return (new \App\Review\Repository\ReviewRepository)->select()->where('`product_id` = ?',[ $this->getId()])->get();;
    }

}
