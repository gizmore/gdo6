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
 * @version 6.10.4
 * @since 6.10.2
 */
final class CLI
{
    public static function getUsername()
    {
        return get_current_user();
    }
    
    /**
     * Stop output buffering and start auto flush for CLI mode.
     */
    public static function autoFlush()
    {
        while (ob_get_level())
        {
            ob_end_flush();
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
        # @TODO use output of locale command?
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'de-DE,de;q=0.9,en-US;q=0.8,en;q=0.7';
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
        $mome = trim(Strings::substrTo($line, ' ', $line), '"');
        $lin = Strings::substrFrom($line, ' ', '');
        
        $matches = null;
        if (preg_match('#^([a-z]+)\\.?([a-z]*)(\\.?)([a-z]*)$#iD', $mome, $matches))
        {
            $mo = $matches[1];
            $me = @$matches[2];
            $exec = $lin ? true : (!!@$matches[3]);
            $button = @$matches[4] ? $matches[4] : null;
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

        if (!$button)
        {
            if ($buttons = $method->getButtons())
            {
                $button = array_keys($buttons)[0];
            }
            
        }
        
        # Parse everything after
        $params = self::parseArgline($lin, $method);
//         Logger::log('cli', 'PARAMS: ' . json_encode($params));
        
        if ($button)
        {
            $params[$button] = $button;
        }
        
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
     * 
     * @param string $line - input line.
     * @param Method $method - method for parameter reference.
     * @param boolean $asValues - convert args to values?
     * 
     * @return string[]
     */
    public static function parseArgline($line, Method $method, $asValues=false)
    {
        $i = 0;
        $success = true;
        $args = Strings::args($line);
        $parameters = [];
        
        # Clear last request errors
        foreach ($method->gdoParameterCache() as $gdt)
        {
            $gdt->error = null;
//             $gdt->var($gdt->initial);
//             $_REQUEST[$gdt->name] = $gdt->var;
//             $_REQUEST[$gdt->formVariable()][$gdt->name] = $gdt->var;
//             $_REQUEST[$gdt->name] = $var;
//             $_REQUEST[$gdt->formVariable()][$gdt->name] = $var;
        }
        
        # Parse optionals --parameter=value
        foreach ($args as $var)
        {
            if (Strings::startsWith($var, '--'))
            {
                $key = Strings::substrTo($var, '=', $var);
                $key = ltrim($key, '-');
                $var = Strings::substrFrom($var, '=', '');
                if ($gdt = $method->gdoParameterByLabel($key))
                {
                    $value = $gdt->toValue($gdt->inputToVar($var));
                    if ($gdt->validate($value))
                    {
                        $gdt->varval($var, $value);
                    }
                    else
                    {
                        $success = false;
                    }
                    $parameters[$gdt->name] = $var;
                    $_REQUEST[$gdt->name] = $var;
                    $_REQUEST[$gdt->formVariable()][$gdt->name] = $var;
                }
                $i++;
                continue;
            }
            break;
        }
        
        # Positional / required params
        foreach ($method->gdoParameterCache() as $gdt)
        {
            if ($gdt->name && $gdt->editable && $gdt->isPositional())
            {
                $var = $gdt->inputToVar(@$args[$i]);
                $value = $gdt->toValue($var);
                if ($gdt->validate($value))
                {
                    $gdt->varval(@$args[$i], $value);
                }
                else
                {
                    $success = false;
                }
                $parameters[$gdt->name] = @$args[$i];
                $_REQUEST[$gdt->name] = @$args[$i];
                $_REQUEST[$gdt->formVariable()][$gdt->name] = $var;
                $i++;
            }
        }
        
        # Convert to values
        if ($asValues)
        {
            $old = $parameters;
            $parameters = [];
            foreach ($method->gdoParameterCache() as $gdt)
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
        
        return $success ? $parameters : false;
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
            if (!$gdt->editable)
            {
                continue;
            }
            if ($gdt->isPositional())
            {
                $usage1[] = sprintf('<%s>(%s)', $gdt->displayLabel(), $gdt->gdoExampleVars());
            }
            else
            {
                $usage2[] = sprintf('[--%s=<%s>(%s)]',
                    $gdt->name, $gdt->gdoExampleVars(), $gdt->getVar());
            }
        }
        $usage = implode(' ', $usage2) . ' ' . implode(' ', $usage1);
        $usage = trim($usage);
        $buttons = self::renderCLIHelpButtons($method);
        $mome = sprintf('%s.%s', 
            $method->getCLITrigger(), $buttons);
        
        return GDT_Response::newWithHTML(t('cli_usage', [
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

# Required gdo constants :(
if (!defined('GDO_DOMAIN')) define('GDO_DOMAIN', 'gdo6.localhost');
if (!defined('GDO_MODULE')) define('GDO_MODULE', 'Core');
if (!defined('GDO_METHOD')) define('GDO_METHOD', 'Welcome');
