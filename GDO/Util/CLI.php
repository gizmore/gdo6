<?php
namespace GDO\Util;

use GDO\Core\ModuleLoader;
use GDO\Core\GDOError;
use GDO\Core\Method;

/**
 * CLI utilities.
 * Can turn cmdlines into Method parameters.
 * 
 * @see Method
 * 
 * @author gizmore
 * @version 6.10.2
 * @since 6.10.2
 */
final class CLI
{
    public static function getUsername()
    {
        return get_current_user();
    }
    
    public static function autoFlush()
    {
        while (ob_get_level())
        {
            ob_end_clean();
        }
        ob_implicit_flush(true);
    }
    
    /**
     * Simulate PHP $_SERVER vars.
     */
    public static function setServerVars()
    {
        $_SERVER['HTTPS'] = 'off';
        $_SERVER['HTTP_HOST'] = GWF_DOMAIN;
        $_SERVER['SERVER_NAME'] = GWF_DOMAIN; # @TODO use machines host name.
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1'; # @TODO use machines IP
        $_SERVER['HTTP_USER_AGENT'] = 'Firefox Gecko MS Opera';
        $_SERVER['REQUEST_URI'] = '/index.php?mo=' . GWF_MODULE . '&me=' . GWF_METHOD;
        $_SERVER['HTTP_REFERER'] = 'http://'.GWF_DOMAIN.'/index.php';
        $_SERVER['HTTP_ORIGIN'] = '127.0.0.2';
        $_SERVER['SCRIPT_NAME'] = '/index.php';
        $_SERVER['SERVER_SOFTWARE']	= 'Apache/2.4.41 (Win64) PHP/7.4.0';
        $_SERVER['HTTPS'] = 'off';
        $_SERVER['PHP_SELF'] = '/index.php';
        $_SERVER['QUERY_STRING'] = 'mo=' . GWF_MODULE . '&me=' . GWF_METHOD;
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'de-DE,de;q=0.9,en-US;q=0.8,en;q=0.7'; # @TODO use output of locale command?
    }
    
    public static function execute($line)
    {
        if (!($line = trim($line, "\r\n\t ")))
        {
            throw new GDOError('err_need_input');
        }
            
        # Parse 'module.method' part
        $moduleName = Strings::substrTo($line, ' ', $line);
        $methodName = Strings::substrFrom($line, '.');
        $moduleName = Strings::substrTo($moduleName, '.', $moduleName);
        $module = ModuleLoader::instance()->getModule($moduleName, true);
        $method = $module->getMethodByName($methodName);
        if (!$method)
        {
            throw new GDOError('err_unnkown_method', [$methodName]);
        }

        # Parse everything after
        if ($argline = Strings::substrFrom($line, ' '))
        {
            self::parseArgline($argline);
        }
        
        # Execute the method
        return $method->exec();
    }
    
    /**
     * Turn a line of text into method parameters.
     * @param string $line
     * @param Method $method
     */
    private static function parseArgline($line, Method $method)
    {
        
    }

}
