<?php 

namespace App\Webshop\Entity;

use Repository\ModuleEntity;

	/**
	 * @FG\Entity[name=WebshopOrderRow,repository=WebshopOrderRepository,type=Webshop]
	 * Class Faq
	 * @package App\Entity
	 */

class WebshopOrderRow extends ModuleEntity{

	/**
	 * @FG\Column[type=primary]
	 * @FG\listForm[0=]
	 */
	private $id;

	/**
	 * @FG\Column[type=varchar]
	 * @FG\listForm[width=3]
	 * @FG\Filter::default[type=checkbox]
	 */
	private $title;

	/**
	 * @FG\Column[type=float,length={11,2}]
	 * @FG\listForm[ignore=true,width=3]
	 * @FG\Filter::default[type=checkbox]
	 */
	private $price;

	/**
	 * @FG\Column[type=float,length={11,2}]
	 * @FG\listForm[ignore=true,width=3]
	 * @FG\Filter::default[type=checkbox]
	 */
	private $vat;

	/**
	 * @FG\Column[type=float,length={11,2}]
	 * @FG\listForm[ignore=true,width=3]
	 */
	private $amount;
	/**
	 * @FG\Column[type=int]
	 * @FG\listForm[width=3]
	 */
	private $orderId;

	public function __construct()
    {
    }

	public function setId( $value)
	{
		$this->id= $value;
	}
	public function getId()
	{
		return $this->id;
	}
	public function setPrice( $value)
	{
		$this->price= $value;
	}
	public function getPrice()
	{
		return $this->price;
	}
	public function setAmount( $value)
	{
		$this->amount= $value;
	}
	public function getAmount()
	{
		return $this->amount;
	}
	public function setVat( $value)
	{
		$this->vat= $value;
	}
	public function getVat()
	{
		return $this->vat;
	}
	public function setTitle( $value)
	{
		$this->title= $value;
	}
	public function getTitle()
	{
		return $this->title;
	}
	public function setOrderId( $value)
	{
		$this->orderId= $value;
	}
	public function getOrderId()
	{
		return $this->orderId;
	}
}