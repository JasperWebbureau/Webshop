<?php 

namespace App\Webshop\Entity;

use App\EntityTraits\EntityTrait;
use FlexGrid\Repository\EntityRepository;
use Html\Element;
use Repository\ModuleEntity;
use Repository\Repository;
use Repository\RepositoryEntity;
use Utils\Files\ImageFile;

	/**
	 * @FG\Entity[name=WebshopProductGroup,repository=WebshopProductGroupRepository,type=Webshop]
	 * Class WebshopProductGroup
	 * @package App\Entity
	 */

class WebshopProductGroup extends ModuleEntity
{
    //  use EntityTrait;

    
	/**
	 * @FG\Column[type=int,role=hidden]
	 */
	public $isHidden;

	/**
	 * @FG\Column[type=primary]
	 * @FG\listForm[0=]
	 */
	private $id;
	/**
	 * @FG\Column[type=varchar,fill=title,roles=share_title|title]
	 * @FG\listForm[width=3]
	 */
	private $title;

	/**
	 * @FG\Column[type=varchar]
	 * @FG\listForm[width=3]
	 */
	private $teaserTitle;
	/**
	 * @FG\Column[type=image,roles=share_image|main_image]
	 * @FG\listForm[width=5,label=test,placeholder=Image]
	 */
	private $image;


	/**
	 * @FG\Column[type=html,roles=description]
	 * @FG\listForm[ignore=true,width=3]
	 */
	private $intro;


	/**
	 * Portfolio constructor.
	 */
	public function __construct()
    {

    }
	
	public function setIsHidden( $value)
	{
		$this->isHidden= $value;
	}
	public function getIsHidden()
	{
		return $this->isHidden;
	}
	public function setId( $value)
	{
		$this->id= $value;
	}
	public function getId()
	{
		return $this->id;
	}
	public function setTitle( $value)
	{
		$this->title= $value;
	}
	public function getTitle()
	{
		return $this->title;
	}
	public function setImage( $value)
	{
		$this->image= $value;
	}
	public function getImage()
	{
		  if ( $this->image != '') { return new \Utils\Files\ImageFile($this->image); } else { return new \Utils\Files\ImageFile(""); }
	}
	public function setIntro( $value)
	{
		$this->intro= $value;
	}
	public function getIntro()
	{
		return $this->intro;
	}

	public function getProductChildren()
	{
		return (new \App\Repository\ProductRepository())->select()->where('`group_id` = ?',[ $this->getId()])->get();
	}
	public function setTeaserTitle( $value)
	{
		$this->teaserTitle= $value;
	}
	public function getTeaserTitle()
	{
		return $this->teaserTitle;
	}
	public function getWebshopProductChildren()
	{
		return (new \App\Webshop\Repository\WebshopProductRepository())->select()->where('`group_id` = ?',[ $this->getId()])->get();
	}
}