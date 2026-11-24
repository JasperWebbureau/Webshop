<?php

namespace Flexgrid\Modules\Webshop\Entity;

use Flexgrid\Modules\Webshop\Repository\WebshopMoodboardItemRepository;
use Flexgrid\Utils\Files\ImageFile;
use Repository\ModuleEntity;

/**
 * @FG\Entity[name=WebshopMoodboard,label=Moodboard,repository=Flexgrid\Modules\Webshop\Repository\WebshopMoodboardRepository,type=Webshop,in_menu=true,defaultOrder=order:ASC]
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



    public function getWebshopMoodboardItemChildren(): array
    {
        if ((int)$this->getId() <= 0) {
            return [];
        }

        return (new WebshopMoodboardItemRepository())->getByMoodboardId((int)$this->getId());
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

}
