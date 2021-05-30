<?php
namespace GDO\Util;

use GDO\Core\GDOParameterException;
use GDO\Core\GDT_Error;
use GDO\Core\ModuleLoader;
use GDO\Core\GDOError;
use GDO\Core\Method;
use GDO\Core\GDT;
use GDO\Core\GDT_Response;
use GDO\Core\GDO_Module;
use GDO\Form\GDT_Submit;

/**
 * CLI utilities.
 * Can turn cmdlines into Method parameters.
 * 
 * @see Method
 * 
 * @author gizmore
 * @version 6.10.3
 * @since 6.10.2
 */
final class CLI
{
    public static function getUsername()
    {
        return get_current_user();
    }
    
//     public static function autoFlush()
//     {
//         while (ob_get_level())
//         {
//             ob_end_clean();
//         }
//         ob_implicit_flush(true);
//     }
    
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
        $mome = Strings::substrTo($line, ' ', $line);
        $lin = Strings::substrFrom($line, ' ', '');
        
        $matches = null;
        if (preg_match('#^([a-z]+)\\.?([a-z]*)(\\.?)([a-z]*)$#iD', $mome, $matches))
        {
            $mo = $matches[1];
            $me = @$matches[2];
            $exec = $lin ? true : (!!@$matches[3]);
            $button = @$matches[4] ? $matches[4] : 'submit';
        }
        else
        {
            throw new GDOError('err_module_method');
        }
        
        if (!($module = ModuleLoader::instance()->getModule($mo, true)))
        {
            throw new GDOError('err_module_method');
        }
        
        if (!$module->isEnabled())
        {
            return GDT_Error::responseWith('err_method_disabled');
        }
        
        if (!$me)
        {
            return self::showMethods($module);
        }
        
        $method = $module->getMethodByName($me);
        if (!$method)
        {
            throw new GDOError('err_module_method');
        }
        
        if (!$exec)
        {
            return self::showHelp($method);
        }

        # Parse everything after
        $params = self::parseArgline($lin, $method);
        
        $params[$button] = $button;
        $method->requestParameters($params);
        
        # Execute the method
        try
        {
            return $method->exec();
        }
        catch (GDOParameterException $ex)
        {
            return GDT_Response::makeWithHTML($ex->getMessage());
        }
    }
    
    private static function showHelp(Method $method)
    {
        return $method->renderCLIHelp();
    }
    
    private static function showMethods(GDO_Module $module)
    {
        $methods = $module->getMethods();
        
        $methods = array_filter($methods, function(Method $method) {
            return (!$method->isAjax()) && $method->isCLI();
        });
        
        $methods = array_map(function(Method $m) {
            return $m->gdoShortName();
        }, $methods);
        
        return GDT_Response::makeWithHTML(t('cli_methods', [
            $module->displayName(), implode(', ', $methods)]));
    }
    
    /**
     * Turn a line of text into method parameters.
     * @param string $line
     * @param Method $method
     * @return string[]
     */
    public static function parseArgline($line, Method $method, $asValues=false)
    {
        $parameters = [];
        
        while (Strings::startsWith($line, '--'))
        {
            $k = Strings::substrTo($line, '=', $line);
            $k = trim($k, '-');
            $v = Strings::substrFrom($line, '=', '');
            $v = Strings::substrTo($v, ' ', $v);
            $v = $v === '' ? null : $v;
            $line = Strings::substrFrom($line, ' ', '');
            $parameters[$k] = $v;
        }
        
        $i = 0;
        $args = Strings::args($line);
        
        foreach ($method->allParameters() as $gdt)
        {
            if ($gdt->name && $gdt->editable && $gdt->isPositional())
            {
                $arg = @$args[$i++];
                $gdt->var($arg);
                $parameters[$gdt->name] = $arg;
            }
        }
        
        if ($asValues)
        {
            $old = $parameters;
            $parameters = [];
            foreach ($method->allParameters() as $gdt)
            {
                if (isset($old[$gdt->name]))
                {
                    $parameters[$gdt->name] = $gdt->getValue();
                }
                else
                {
                    $parameters[$gdt->name] = $gdt->getInitialValue();
                }
            }
        }
        
        return $parameters;
    }

    /**
     * Render help line for gdt parameters.
     * @param GDT[] $fields
     * @return string
     */
    public static function renderCLIHelp(Method $method, array $fields)
    {
        $usage1 = [];
        $usage2 = [];
        foreach ($fields as $gdt)
        {
            if ( (!$gdt->isSerializable()) ||
                 (!$gdt->editable) )
            {
                continue;
            }
            if ($gdt->isPositional())
            {
                $usage1[] = sprintf('<%s>', $gdt->name);
            }
            else
            {
                $usage2[] = sprintf('[--%s=<%s>]', $gdt->name, $gdt->gdoShortName());
            }
        }
        $usage = implode(' ', $usage2) . ' ' . implode(' ', $usage1);
        $usage = trim($usage);
        $buttons = self::renderCLIHelpButtons($method);
        $mome = sprintf('%s.%s.%s', 
            $method->getModuleName(), $method->getMethodName(), $buttons);
        return GDT_Response::makeWithHTML(t('cli_usage', [
            trim(strtolower($mome).' '.$usage), $method->getDescription()]));
    }
    
    private static function renderCLIHelpButtons(Method $method)
    {
        $impl = [];
        $buttons = $method->getButtons();
        foreach ($buttons as $gdt)
        {
            if (!($gdt instanceof GDT_Submit))
            {
                continue;
            }
            if ($gdt->name === 'submit')
            {
                continue;
            }
            $impl[] = $gdt->name;
        }
        return $impl ? '[' . implode('|', $impl) . ']' : '';
    }
    
}
