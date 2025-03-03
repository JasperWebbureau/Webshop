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
	 * @FG\Entity[name=WebshopProduct,repository=WebshopRepository,type=Webshop]
	 * Class Blog
	 * @package App\Entity
	 */

class WebshopProduct extends ModuleEntity
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
	 * @FG\listForm[width=6]
	 */
	private $title;
	/**
	 * @FG\Column[type=image,roles=share_image|main_image]
	 * @FG\listForm[ignore=1,label=test,placeholder=Image]
	 */
	private $image;


	/**
	 * @FG\Column[type=html,roles=description]
	 * @FG\listForm[ignore=true,width=3]
	 */
	private $intro;


	/**
	 * @FG\Column[type=html,roles=description]
	 * @FG\listForm[ignore=true]
	 */
	private $description;

	/**
	 * @FG\Column[type=varchar,roles=specification,spec_title=Formaat,option=Formaat]
	 * @FG\listForm[ignore=true]
	 */
	private $badge;

	/**
	 * @FG\Column[type=varchar,roles=specification,spec_title=Formaat,option=Formaat]
	 * @FG\listForm[ignore=true]
	 */
	private $size;

	/**
	 * @FG\Column[type=varchar,roles=specification,spec_title=Extra]
	 * @FG\listForm[ignore=true]
	 */
	private $extra;

	/**
	 * @FG\Column[type=varchar,roles=specification,spec_title=Type,option=Type]
	 * @FG\listForm[ignore=true]
	 */
	private $type;





	/**
	 * @FG\Column[type=float,length={11,2}]
	 * @FG\listForm[ignore=true,width=3]
	 * @FG\Filter::default[type=checkbox]
	 */
	private $price;

	/**
	 * @FG\Column[type=float,length={11,2}]
	 * @FG\listForm[ignore=true]
	 */
	private $vat;

	/**
	 * @FG\Column[type=float,length={11,2}]
	 * @FG\listForm[ignore=true,width=3]
	 * @FG\Filter::default[type=checkbox]
	 */
	private $priceDiscount;


	/**
	 * @FG\Column[type=int]
	 * @FG\listForm[ignore=true,width=3]
	 */
	private $stock;
	/**
	 * @FG\Column[type=int,parent=App\Webshop\Entity\WebshopProductGroup::id,role=group]
	 * @FG\listForm[ignore=true]
	 */
	private $groupId;


	/**
	 * @FG\Column[type=int,roles=specification,parent=App\Entity\Product::id]
	 * @FG\listForm[ignore=true]
	 */
	private $mainArticle;

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
	public function setPrice( $value)
    {
        $this->price= $value;
    }
	public function getPrice()
    {
        return $this->price;
    }

	public function getPriceHtml()
    {
        $children = $this->getProductChildren();


        $price= new Element('price');
        if(empty($children)){
            $parts = explode('.', $this->getPrice());
        }else{
            $min = $this->getPrice();
            foreach($children as $child){
                $min = min($child->getPrice(), $min);
            }
            $parts = explode('.', $min);
            $from = '<span>Vanaf</span>';
        }

        $price->append($from.'<span>€</span><span>'. $parts[0]. '</span><span>,</span><span>'. $parts[1]. '</span>');
        return $price;
    }
	public function setGroupId( $value)
    {
        $this->groupId= $value;
    }
	public function getGroupId()
    {
        return $this->groupId;
    }
	public function getProductGroupParent()
    {
        return (new \App\Repository\ProductGroupRepository())->select()->where('id = ?',[ $this->getGroupId()])->get();
    }
	public function setImage2( $value)
    {
        $this->image2= $value;
    }
	public function getImage2()
    {
        if ( $this->image2 != '') { return new \Utils\Files\ImageFile($this->image2); } else { return new \Utils\Files\ImageFile(""); }
    }
	public function setDescription( $value)
    {
        $this->description= $value;
    }
	public function getDescription()
    {
        return $this->description;
    }
	public function setStock( $value)
    {
        $this->stock= $value;
    }
	public function getStock()
    {
        return $this->stock;
    }

	public function setSize( $value)
    {
        $this->size= $value;
    }
	public function getSize()
    {
        return $this->size;
    }
	public function setExtra( $value)
    {
        $this->extra= $value;
    }
	public function getExtra()
    {
        return $this->extra;
    }
	public function setMainArticle( $value)
    {
        $this->mainArticle= $value;
    }
	public function getMainArticle()
    {
        return $this->mainArticle;
    }
	public function getProductChildren()
    {
        return (new \App\Repository\ProductRepository())->select()->where('`main_article` = ?',[ $this->getId()])->get();
    }
	public function getProductParent()
    {
        return (new \App\Repository\ProductRepository())->select()->where('id = ?',[ $this->getMainArticle()])->get();
    }

	public function getOptions()
    {
        $children = $this->getProductChildren();

        if($this->getMainArticle() == 0 && empty($children) ){
            //not a main article nor a child of
            return [];
        }
        if(empty($children)){
            // this is a main article
            $children = $this->getProductParent()[0]->getProductChildren();
        }


        $fields = $this->getFields();
        $optionFields = [];
        foreach($fields as $field){

            if($field['option'] != ''){
                $optionFields['fields'][$field['option']] = $field;

                foreach($children as $child){
                    $getter = $field['getter_name'];
                    $optionFields['fields'][$field['option']]['values'][] = $child->$getter();
                }
            }

        }

        $optionFields['children'] = $children;

        return $optionFields;
    }
	public function setColor( $value)
    {
        $this->color= $value;
    }
	public function getColor()
    {
        return $this->color;
    }
	public function setType( $value)
    {
        $this->type= $value;
    }
	public function getType()
    {
        return $this->type;
    }
	public function setCatagorie( $value)
    {
        $this->catagorie= $value;
    }
	public function getCatagorie()
    {
        return $this->catagorie;
    }
	public function setPriceDiscount( $value)
    {
        $this->priceDiscount= $value;
    }
	public function getPriceDiscount()
    {
        return $this->priceDiscount;
    }
	public function setBadge( $value)
    {
        $this->badge= $value;
    }
	public function getBadge()
    {
        return $this->badge;
    }
	public function getWebshopProductImageChildren()
	{
		return (new \App\Webshop\Repository\WebshopProductImageRepository())->select()->where('`group_id` = ?',[ $this->getId()])->get();
	}
	public function getWebshopProductGroupParent()
	{
		return (new \App\Webshop\Repository\WebshopProductGroupRepository())->select()->where('id = ?',[ $this->getGroupId()])->get()[0];
	}
	public function setVat( $value)
	{
		$this->vat= $value;
	}
	public function getVat()
	{
		return $this->vat;
	}
}