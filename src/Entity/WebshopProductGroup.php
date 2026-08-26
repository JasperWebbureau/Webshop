<?php

namespace Flexgrid\Modules\Webshop\Entity;

use Flexgrid\Utils\Files\ImageFile;
use Repository\ModuleEntity;

/**
 * @FG\Entity[name=WebshopProductGroup,repository=Flexgrid\Modules\Webshop\Repository\WebshopProductGroupRepository,type=Webshop,in_menu=true,defaultOrder=title:ASC]
 */
class WebshopProductGroup extends ModuleEntity
{
    /**
     * @FG\Column[type=primary]
     * @FG\listForm[0=]
     */
    protected $id;

    /**
     * @FG\Column[type=varchar,fill=title,roles=share_title|title,search=true,sortoption={asc:A-z,desc:Z-a}]
     * @FG\Filter::default[type=textsearch,html_title=Zoeken,fields={title,description}]
     * @FG\listForm[width=4]
     */
    protected $title;

    /**
     * @FG\Column[type=int,parent=Flexgrid\Modules\Webshop\Entity\WebshopProductMainGroup::id,label=Hoofdgroep]
     * @FG\Filter::default[type=checkboxMultipleSelect,html_title=Hoofdgroep]
     * @FG\listForm[width=3]
     */
    protected $mainGroupId;

    /**
     * @FG\Column[type=simplehtml,input_type=html]
     * @FG\listForm[ignore=true]
     */
    protected $description;

    /**
     * @FG\Column[type=image,roles=share_image|main_image]
     * @FG\listForm[ignore=true]
     */
    protected $image;



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

    public function getMainGroupId()
    {
        return (int)$this->mainGroupId;
    }

    public function setMainGroupId($value)
    {
        $this->mainGroupId = (int)$value;
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

    public function getImage()
    {
        return ImageFile::getImageFile($this->image);
    }

    public function setImage($value)
    {
        $this->image = $value;
        return $this;
    }

    public function getIsActive()
    {
        return $this->isActive;
    }

    public function setIsActive($value)
    {
        $this->isActive = $value;
        return $this;
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
     * Get the children of WebshopProduct
     */
    public function getWebshopProductChildren()
    {
        return (new \Flexgrid\Modules\Webshop\Repository\WebshopProductRepository)->select()->where('`group_id` = ?',[ $this->getId()])->get();;
    }

    public function getWebshopProductMainGroupParent()
    {
        $id = $this->getMainGroupId();
        if (empty($id)) {
            return null;
        }

        if (!isset($this->__autowireParentCache['mainGroupIdParent']) ||
            $this->__autowireParentCache['mainGroupIdParent']['id'] != $id) {
            $parent = (new \Flexgrid\Modules\Webshop\Repository\WebshopProductMainGroupRepository())->select()->where('id = ?', [$id])->get()[0] ?? null;
            $this->__autowireParentCache['mainGroupIdParent'] = [
                'id' => $id,
                'value' => $parent,
            ];
        }

        return $this->__autowireParentCache['mainGroupIdParent']['value'];
    }

    public function getWebshopProductMainGroupValue($getter = null)
    {
        $parent = $this->getWebshopProductMainGroupParent();
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

}
