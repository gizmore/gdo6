<?php
namespace GDO\Util;

use GDO\Core\ModuleLoader;
use GDO\Core\GDOError;
use GDO\Core\Method;
use GDO\DB\GDT_Text;

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
        $_SERVER['HTTP_HOST'] = GDO_DOMAIN;
        $_SERVER['SERVER_NAME'] = GDO_DOMAIN; # @TODO use machines host name.
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1'; # @TODO use machines IP
        $_SERVER['HTTP_USER_AGENT'] = 'Firefox Gecko MS Opera';
        $_SERVER['REQUEST_URI'] = '/index.php?mo=' . GDO_MODULE . '&me=' . GDO_METHOD;
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['HTTP_REFERER'] = 'http://'.GDO_DOMAIN.'/index.php';
        $_SERVER['HTTP_ORIGIN'] = '127.0.0.2';
        $_SERVER['SCRIPT_NAME'] = '/index.php';
        $_SERVER['SERVER_SOFTWARE']	= 'Apache/2.4.41 (Win64) PHP/7.4.0';
        $_SERVER['HTTPS'] = 'off';
        $_SERVER['PHP_SELF'] = '/index.php';
        $_SERVER['QUERY_STRING'] = 'mo=' . GDO_MODULE . '&me=' . GDO_METHOD;
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'de-DE,de;q=0.9,en-US;q=0.8,en;q=0.7'; # @TODO use output of locale command?
    }
    
    public static function br2nl($s, $nl=PHP_EOL)
    {
        return preg_replace('#< *br */? *>#is', $nl, $s);
    }
    
    public static function htmlToCLI($html)
    {
        $html = preg_replace('/<a .*href="([^"]+)".*>([^<]+)<\\/a>/ius', "$1 ($2)", $html);
        $html = self::br2nl($html);
        $html = preg_replace('/<[^>]*>/is', '', $html);
        $html = html_entity_decode($html, ENT_QUOTES, 'UTF-8');
        return $html;
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
     * @return string[]
     */
    private static function parseArgline($line, Method $method)
    {
        $parameters = [];
        
        while (Strings::startsWith($line, '--'))
        {
            $n = Strings::substrTo($line, ' ', $line);
            $n = trim($n, '-');
            $l = Strings::substrFrom($line, ' ', $line);
            $a = Strings::substrTo($l, ' ', $l);
            $gdt = $method->gdoParameter($n);
            $parameters[$gdt->name] = $a;
            $line = Strings::substrFrom($l, ' ', $l);
        }
        
        foreach ($method->gdoParameterCache() as $gdt)
        {
            if ($gdt->name && $gdt->editable && $gdt->isPositional())
            {
                if ($gdt instanceof GDT_Text)
                {
                    $parameters[$gdt->name] = trim($line, '"');
                    break;
                }
                else
                {
                    $args = Strings::args($line);
                    $arg = $args[0];
                    $parameters[$gdt->name] = trim($arg, '"');
                    $line = mb_substr($line, 0, mb_strlen($arg) + 1);
                }
            }
        }
        return $parameters;
    }

}
