<?php

namespace Flexgrid\Modules\Webshop\Entity;

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

    public function getIsActive()
    {
        return $this->isActive;
    }

    public function setIsActive($value)
    {
        $this->isActive = $value;
        return $this;
    }

    public function getWebshopProductGroupChildren()
    {
        return (new \Flexgrid\Modules\Webshop\Repository\WebshopProductGroupRepository())->select()->where('`main_group_id` = ?', [$this->getId()])->get();
    }
}
