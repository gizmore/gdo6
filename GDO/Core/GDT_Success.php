<?php
namespace GDO\Core;
use GDO\UI\GDT_Panel;
/**
 * A success message, the pedant to GDT_Error.
 * @author gizmore
 * @since 6.00
 * @version 6.05
 * @see GDT_Error
 */
class GDT_Success extends GDT_Panel
{
    public static function responseWith($key, array $args=null, $code=200, $log=true)
    {
        return GDT_Response::makeWith(self::with($key, $args, $code, $log));
    }
    
    public static function with($key, array $args=null, $code=200, $log=true)
    {
        if ($log)
        {
            Logger::logMessage(tiso('en', $key, $args));
        }
        return self::withHTML(t($key, $args));
    }
    
    public static function withHTML($html)
    {
        return self::make()->html($html)->icon('check');
    }
    
    ##############
    ### Render ###
    ##############
    public function renderJSON()
    {
        return array(
            'message' => $this->html,
        );
    }
}
