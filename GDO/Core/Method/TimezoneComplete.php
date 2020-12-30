<?php
namespace GDO\Core\Method;

use GDO\Core\MethodCompletion;
use GDO\Date\GDT_Timezone;
use GDO\Core\Website;

final class TimezoneComplete extends MethodCompletion
{
    public function execute()
    {
        $q = $this->getSearchTerm();
        $json = [];
        foreach (GDT_Timezone::make()->choices as $tz)
        {
            if (stripos($tz, $q) !== false)
            {
                $json[] = [
                    'id' => $tz,
                    'text' => $tz,
                    'display' => $tz,
                ];
            }
        }
        
        Website::renderJSON($json);
    }
    
}
