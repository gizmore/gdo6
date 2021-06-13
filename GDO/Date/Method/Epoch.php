<?php
namespace GDO\Date\Method;

use GDO\Core\Method;
use GDO\DB\GDT_EnumNoI18n;

/**
 * Print the unix timestamp.
 * @author gizmore
 * @version 6.10.4
 * @since 6.10.4
 */
final class Epoch extends Method
{
    public function gdoParameters()
    {
        return [
            GDT_EnumNoI18n::make('format')->enumValues('unix', 'java', 'micro')->notNull()->initial('unix'),
        ];
    }
    
    public function execute()
    {
        $format = $this->gdoParameterVar('format');
        switch ($format)
        {
            case 'unix': $time = time(); break;
            case 'java': $time = round(microtime(true)*1000.0); break;
            case 'micro': $time = microtime(true); break;
        }
        $key = 'msg_time_'.$format;
        return $this->message($key, [$time]);
    }
    
}
