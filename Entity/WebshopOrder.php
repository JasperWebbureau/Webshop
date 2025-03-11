<?php 

namespace App\Webshop\Entity;

use App\Webshop\Webshop\Orders;
use Mollie\Api\Resources\Order;
use Repository\ModuleEntity;
use Utils\_Mail;

	/**
	 * @FG\Entity[name=WebshopOrder,repository=WebshopOrderRepository,type=Webshop]
	 * Class Faq
	 * @package App\Entity
	 */

class WebshopOrder extends ModuleEntity{
	/**
	 * @FG\Column[type=primary]
	 * @FG\listForm[0=]
	 */
	private $id;

	/**
	 * @FG\Column[type=int]
	 * @FG\listForm[0=]
	 */
	private $status;

	/**
	 * @FG\Column[type=int]
	 * @FG\listForm[0=]
	 */
	private $clientId;

	/**
	 * @FG\Column[type=int]
	 * @FG\listForm[0=]
	 */
	private $addressId;

	/**
	 * @FG\Column[type=varchar]
	 * @FG\listForm[0=]
	 */
	private $paymentLink;

	/**
	 * @FG\Column[type=varchar]
	 * @FG\listForm[0=]
	 */
	private $paymentId;

	/**
	 * @FG\Column[type=tinyInt]
	 */
	private $isPaid;

	public function setId( $value)
	{
		$this->id= $value;
	}
	public function getId()
	{
		return $this->id;
	}
	public function setPaid( $value)
	{
		$this->paid= $value;
	}
	public function getPaid()
	{
		return $this->paid;
	}
	public function setClientId( $value)
	{
		$this->clientId= $value;
	}
	public function getClientId()
	{
		return $this->clientId;
	}
	public function setAddressId( $value)
	{
		$this->addressId= $value;
	}
	public function getAddressId()
	{
		return $this->addressId;
	}
	public function setPaymentLink( $value)
	{
		$this->paymentLink= $value;
	}
	public function getPaymentLink()
	{
		return $this->paymentLink;
	}
	public function setStatus( $value)
	{
		$this->status= $value;
	}
	public function getStatus()
	{
		return $this->status;
	}
	public function setIsPaid( $value)
	{
	    if($this->getIsPaid() && (int)$value != 0){
            $orders= new Orders();
            $orders->notifyOwnerAboutOrderPayment($this);
        }
		$this->isPaid= $value;
	}
	public function getIsPaid()
	{
		return $this->isPaid;
	}
	public function setPaymentId( $value)
	{
		$this->paymentId= $value;
	}
	public function getPaymentId()
	{
		return $this->paymentId;
	}
}