<?php
namespace GDO\Country\Method;

use GDO\Country\GDO_Country;
use GDO\Core\GDT_Array;
use GDO\Core\MethodAjax;

/**
 * AJAX List of all countries.
 * 
 * @author gizmore
 * @version 6.11.0
 * @since 6.10.1
 */
final class AjaxList extends MethodAjax
{
    public function execute()
    {
        $json = array_map(function(GDO_Country $country) {
            return [
                'id' => $country->getID(),
                'text' => $country->displayName(),
                'display' => $country->renderChoice(),
            ];
        }, GDO_Country::table()->allCached());
        
        return GDT_Array::makeWith(array_values($json));
    }
    
}
