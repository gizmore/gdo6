<?php
namespace GDO\Date\Method;

use GDO\Core\Method;
use GDO\DB\GDT_EnumNoI18n;
use GDO\Core\Application;

/**
 * Print the unix timestamp, and other formats. Unused?
 * 
 * @author gizmore
 * @version 6.10.6
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
            case 'unix': $time = Application::$TIME; break;
            case 'java': $time = round(Application::$MICROTIME*1000.0); break;
            case 'micro': $time = Application::$MICROTIME; break;
        }
        $key = 'msg_time_'.$format;
        return $this->message($key, [$time]);
    }
    
}
