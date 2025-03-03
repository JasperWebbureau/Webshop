<?php 

namespace App\Webshop\Entity;

use Repository\ModuleEntity;

	/**
	 * @FG\Entity[name=WebshopDeliveryMethod,repository=WebshopDeliveryMethodRepository,type=Webshop]
	 * Class Faq
	 * @package App\Entity
	 */

class WebshopDeliveryMethod extends ModuleEntity
{
	/**
	 * @FG\Column[type=primary]
	 * @FG\listForm[0=]
	 */
	private $id;

	/**
	 * @FG\Column[type=int,role=hidden]
	 */
	public $isHidden;


	/**
	 * @FG\Column[type=int,role=order]
	 */
	public $order;

	/**
	 * @FG\Column[type=varchar,fill=title,role=title]
	 * @FG\listForm[width=6]
	 */
	private $title;

	/**
	 * @FG\Column[type=float,length={11,2}]
	 * @FG\listForm[ignore=true,width=3]
	 */
	private $price;

	public function setId( $value)
	{
		$this->id= $value;
	}
	public function getId()
	{
		return $this->id;
	}
	public function setIsHidden( $value)
	{
		$this->isHidden= $value;
	}
	public function getIsHidden()
	{
		return $this->isHidden;
	}
	public function setOrder( $value)
	{
		$this->order= $value;
	}
	public function getOrder()
	{
		return $this->order;
	}
	public function setTitle( $value)
	{
		$this->title= $value;
	}
	public function getTitle()
	{
		return $this->title;
	}
	public function setPrice( $value)
	{
		$this->price= $value;
	}
	public function getPrice()
	{
		return $this->price;
	}
}