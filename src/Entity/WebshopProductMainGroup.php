<?php

namespace Flexgrid\Modules\Webshop\Entity;

use Flexgrid\Modules\Webshop\Service\ProductGroupImageService;
use Flexgrid\Utils\Files\ImageFile;
use Repository\ModuleEntity;

/**
 * @FG\Entity[name=WebshopProductMainGroup,repository=Flexgrid\Modules\Webshop\Repository\WebshopProductMainGroupRepository,type=Webshop,in_menu=true,defaultOrder=title:ASC]
 */
class WebshopProductMainGroup extends ModuleEntity
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
     * @FG\Column[type=simplehtml,input_type=html]
     * @FG\listForm[ignore=true]
     */
    protected $description;

    /**
     * @FG\Column[type=varchar,label=Intro zin]
     * @FG\listForm[ignore=true]
     */
    protected $introSentence;

    /**
     * @FG\Column[type=image,roles=share_image|main_image]
     * @FG\listForm[ignore=true]
     */
    protected $image;

    /**
     * @FG\Column[type=varchar,label=CTA titel]
     * @FG\listForm[ignore=true]
     */
    protected $ctaTitle;

    /**
     * @FG\Column[type=textarea,label=CTA tekst]
     * @FG\listForm[ignore=true]
     */
    protected $ctaDescription;

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

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($value)
    {
        $this->description = $value;
        return $this;
    }

    public function getIntroSentence()
    {
        return $this->introSentence;
    }

    public function setIntroSentence($value)
    {
        $this->introSentence = $value;
        return $this;
    }

    public function getImage()
    {
     //   dump($this->image);
        if (is_string($this->image) && (int)$this->getId() > 0 &&
             ! is_numeric($this->image)) {
            $mediaId = (new ProductGroupImageService())->cloneFirstProductImageForMainGroup((int)$this->getId(), static::class);
            if ($mediaId > 0) {
                $this->setImage($mediaId);
                $this->save();
            }
        }


        return ImageFile::getImageFile($this->image);
    }

    public function setImage($value)
    {
        $this->image = $value;
        return $this;
    }

    public function getCtaTitle()
    {
        return $this->ctaTitle;
    }

    public function setCtaTitle($value)
    {
        $this->ctaTitle = $value;
        return $this;
    }

    public function getCtaDescription()
    {
        return $this->ctaDescription;
    }

    public function setCtaDescription($value)
    {
        $this->ctaDescription = $value;
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



    public function getWebshopProductGroupChildren()
    {
        return (new \Flexgrid\Modules\Webshop\Repository\WebshopProductGroupRepository())->select()->where('`main_group_id` = ?', [$this->getId()])->get();
    }


    // --- Auto-generated getters and setters ---

    /**
     * Get the parent entity for translationGroupId.
     */
    public function getTranslationGroupParent()
    {
        $id = $this->getTranslationGroupId();
        if (empty($id)) {
            return null;
        }

        if (!isset($this->__autowireParentCache['translationGroupIdParent']) ||
            $this->__autowireParentCache['translationGroupIdParent']['id'] != $id) {
            $parent = (new \Flexgrid\App\Language\Repository\TranslationGroupRepository())->select()->where('id = ?', [$id])->get()[0] ?? null;
            $this->__autowireParentCache['translationGroupIdParent'] = [
                'id' => $id,
                'value' => $parent,
            ];
        }

        return $this->__autowireParentCache['translationGroupIdParent']['value'];
    }

    /**
     * Get a display value for the parent entity of translationGroupId.
     */
    public function getTranslationGroupValue($getter = null)
    {
        $parent = $this->getTranslationGroupParent();
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
     * Get the children of WebshopProductGroup
     */
    public function getFlexgridModulesWebshopEntityWebshopProductGroupChildren()
    {

        return (new \Flexgrid\Modules\Webshop\Repository\WebshopProductGroupRepository)->select()->where('`main_group_id` = ?',[ $this->getId()])->get();;
    }

}
