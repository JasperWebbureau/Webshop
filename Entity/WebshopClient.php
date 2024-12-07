<?php 

namespace App\Webshop\Entity;

use Repository\ModuleEntity;

	/**
	 * @FG\Entity[name=WebshopClient,repository=WebshopClientRepository,type=Webshop]
	 * Class Faq
	 * @package App\Entity
	 */

class WebshopClient  extends ModuleEntity{
	/**
	 * @FG\Column[type=primary]
	 * @FG\listForm[0=]
	 */
	private $id;
	

	
	/**
	 * @FG\Column[type=varchar,fill=title,roles=share_title|title]
	 * @FG\listForm[width=3]
	 */
	private $name;

	
	/**
	 * @FG\Column[type=varchar,fill=title,roles=share_title|title]
	 * @FG\listForm[width=3]
	 */
	private $surname;
	

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
	public function setName( $value)
	{
		$this->name= $value;
	}
	public function getName()
	{
		return $this->name;
	}
	public function setSurname( $value)
	{
		$this->surname= $value;
	}
	public function getSurname()
	{
		return $this->surname;
	}
}