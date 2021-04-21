<?php
namespace GDO\Country\Method;

use GDO\Country\GDO_Country;
use GDO\Core\Website;
use GDO\Core\MethodAjax;

/**
 * AJAX List of all countries.
 * @author gizmore
 * @version 6.10.1
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
        Website::renderJSON(array_values($json));
    }
    
}
