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
	 * @FG\clientOrder[ignore=true]
	 */
	private $id;
	

	
	/**
	 * @FG\Column[type=varchar,fill=title,roles=share_title|title]
	 * @FG\listForm[width=3]
	 * @FG\clientOrder[width=6,label=Naam]
	 */
	private $name;

	
	/**
	 * @FG\Column[type=varchar,fill=title,roles=share_title|title]
	 * @FG\listForm[width=6]
	 * @FG\clientOrder[width=6,label=Achternaam]
	 */
	private $surname;
	/**
	 * @FG\Column[type=email,0=fill]
	 * @FG\listForm[width=3]
	 * @FG\clientOrder[width=6,label=Email]
	 */
	private $email;
	/**
	 * @FG\Column[type=varchar]
	 * @FG\listForm[width=6]
	 * @FG\clientOrder[width=6,label=Telefoonnummer]
	 */
	private $phone;


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
	public function setEmail( $value)
	{
		$this->email= $value;
	}
	public function getEmail()
	{
		return $this->email;
	}
	public function setPhone( $value)
	{
		$this->phone= $value;
	}
	public function getPhone()
	{
		return $this->phone;
	}
}