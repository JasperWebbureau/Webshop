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
	 * @FG\Entity[name=WebshopProductImage,repository=WebshopRepository,type=Webshop]
	 * Class Blog
	 * @package App\Entity
	 */

class WebshopProductImage extends ModuleEntity
{
    //  use EntityTrait;



	/**
	 * @FG\Column[type=primary]
	 * @FG\listForm[0=]
	 */
	private $id;

	/**
	 * @FG\Column[type=image,roles=share_image|main_image]
	 * @FG\listForm[ignore=1,label=test,placeholder=Image]
	 */
	private $image;


	/**
	 * @FG\Column[type=int,parent=App\Webshop\Entity\WebshopProduct::id,role=group]
	 * @FG\listForm[ignore=true]
	 */
	private $groupId;


	public function setId( $value)
	{
		$this->id= $value;
	}
	public function getId()
	{
		return $this->id;
	}
	public function setImage( $value)
	{
		$this->image= $value;
	}
	public function getImage()
	{
		  if ( $this->image != '') { return new \Utils\Files\ImageFile($this->image); } else { return new \Utils\Files\ImageFile(""); }
	}
	public function setGroupId( $value)
	{
		$this->groupId= $value;
	}
	public function getGroupId()
	{
		return $this->groupId;
	}
	public function getWebshopProductParent()
	{
		return (new \App\Webshop\Repository\WebshopProductRepository())->select()->where('id = ?',[ $this->getGroupId()])->get()[0];
	}
}