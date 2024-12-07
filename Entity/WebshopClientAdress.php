<?php 

namespace App\Webshop\Entity;

use Repository\ModuleEntity;

	/**
	 * @FG\Entity[name=WebshopClientAdress,repository=WebshopClientRepository,type=Webshop]
	 * Class Faq
	 * @package App\Entity
	 */

class WebshopClientAdress  extends ModuleEntity{
	/**
	 * @FG\Column[type=primary]
	 * @FG\listForm[0=]
	 */
	private $id;
	/**
	 * @FG\Column[type=varchar,fill=title]
	 * @FG\listForm[width=3]
	 */
	private $street;

	
	/**
	 * @FG\Column[type=varchar,fill=title]
	 * @FG\listForm[width=3]
	 */
	private $streetNr;
	/**
	 * @FG\Column[type=varchar,fill=title]
	 * @FG\listForm[width=3]
	 */
	private $zipCode;
	

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
	public function setStreet( $value)
	{
		$this->street= $value;
	}
	public function getStreet()
	{
		return $this->street;
	}
	public function setSteetNr( $value)
	{
		$this->steetNr= $value;
	}
	public function getSteetNr()
	{
		return $this->steetNr;
	}
	public function setStreetNr( $value)
	{
		$this->streetNr= $value;
	}
	public function getStreetNr()
	{
		return $this->streetNr;
	}
	public function setZipCode( $value)
	{
		$this->zipCode= $value;
	}
	public function getZipCode()
	{
		return $this->zipCode;
	}
}