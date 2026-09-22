<?php

namespace Flexgrid\Modules\Webshop\Entity;

use Flexgrid\Modules\Webshop\Repository\WebshopMoodboardItemRepository;
use Flexgrid\Utils\Files\ImageFile;
use Repository\ModuleEntity;

/**
 * @FG\Entity[name=WebshopMoodboard,repository=Flexgrid\Modules\Webshop\Repository\WebshopMoodboardRepository,type=Webshop,in_menu=true,defaultOrder=order:ASC]
 */
class WebshopMoodboard extends ModuleEntity
{
    /**
     * @FG\Column[type=primary]
     * @FG\listForm[0=]
     */
    protected $id;

    /**
     * @FG\Column[type=varchar,fill=title,roles=share_title|title,search=true,label=Titel]
     * @FG\listForm[width=4]
     */
    protected $title;

    /**
     * @FG\Column[type=varchar,label=Eyebrow]
     * @FG\listForm[width=2]
     */
    protected $eyebrow;

    /**
     * @FG\Column[type=textarea,label=Intro]
     * @FG\listForm[ignore=true]
     */
    protected $intro;

    /**
     * @FG\Column[type=image,roles=share_image|main_image,label=Hoofdafbeelding]
     * @FG\listForm[ignore=true]
     */
    protected $image;

    /**
     * @FG\Column[type=varchar,label=Knoptekst]
     * @FG\listForm[ignore=true]
     */
    protected $buttonText;

    /**
     * @FG\Column[type=int,input_type=checkbox,label=Actief]
     * @FG\listForm[width=1]
     */
    protected $isActive;

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = (int)$value;
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

    public function getEyebrow()
    {
        return $this->eyebrow;
    }

    public function setEyebrow($value)
    {
        $this->eyebrow = $value;
        return $this;
    }

    public function getIntro()
    {
        return $this->intro;
    }

    public function setIntro($value)
    {
        $this->intro = $value;
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

    public function getButtonText()
    {
        return $this->buttonText;
    }

    public function setButtonText($value)
    {
        $this->buttonText = $value;
        return $this;
    }

    public function getIsActive()
    {
        return $this->isActive;
    }

    public function setIsActive($value)
    {
        $this->isActive = (int)$value;
        return $this;
    }

    public function getWebshopMoodboardItemChildren(): array
    {
        if ((int)$this->getId() <= 0) {
            return [];
        }

        return (new WebshopMoodboardItemRepository())->getByMoodboardId((int)$this->getId());
    }
}
