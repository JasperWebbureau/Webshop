<?php
namespace Flexgrid\Modules\Webshop\Service;
use Flexgrid\App\Routing\Routing;

class RoutingService{
    static function getCurrentWebshopProduct()
    {
        $current = Routing::currentEntity();

        if( ! is_object($current)){
            return false;
        }
        if(strpos(get_class($current), 'WebshopProduct') !== false){

            return $current;
        }

        return false;

    }


    static function getCurrentWebshopGroup()
    {
        $current = Routing::currentEntity();

        if( ! is_object($current)){
            return false;
        }
        if(strpos(get_class($current), 'WebshopProductGroup') !== false){

            return $current;
        }

        return false;

    }



}