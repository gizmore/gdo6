<?php
namespace GDO\Country\Method;

use GDO\Core\MethodCompletion;
use GDO\Country\GDO_Country;
use GDO\Core\GDT_Response;
use GDO\Core\GDT_Array;

/**
 * Autocomplete adapter for countries.
 * 
 * @author gizmore
 * @version 6.10.1
 * @since 6.0.0
 */
final class Completion extends MethodCompletion
{
    public function execute()
    {
        $countries = GDO_Country::table()->allCached();
        
        $q = mb_strtolower($this->getSearchTerm());
        $max = $this->getMaxSuggestions();
        $result = [];
        foreach ($countries as $country)
        {
            $name = $country->displayName();
            $iso = $country->getISO();
            if ($q === '')
            {
                $result[] = $country;
            }
            elseif (strtolower($iso) === $q)
            {
                array_unshift($result, $country);
            }
            elseif (mb_stripos($name, $q) !== false)
            {
                $result[] = $country;
            }
            elseif (mb_stripos($country->displayEnglishName(), $q) !== false)
            {
                $result[] = $country;
            }
            if (count($result) >= $max)
            {
                break;
            }
        }
        
        $json = [];
        $json = array_map(function(GDO_Country $country) {
            return [
                'id' => $country->getID(),
                'text' => $country->displayName(),
                'display' => $country->renderChoice(),
            ];
        }, $result);
        
        return GDT_Response::makeWith(GDT_Array::make()->data($json));
    }
    
}
