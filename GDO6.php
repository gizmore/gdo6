<?php
use GDO\Date\Time;
use GDO\Language\Trans;
use GDO\User\GDO_User;
use GDO\Core\GDO;
use GDO\Net\GDT_Url;
use GDO\Util\Common;
use GDO\Core\ModuleLoader;
use GDO\Core\Env;
use GDO\Core\Application;

/**
 * GDO6 autoloader and public functions.
 * @author gizmore
 * @version 6.10.2
 * @since 6.0.0
 */

# performance
define('GDO_PERF_START', microtime(true));

# Autoconf path
define('GDO_PATH', str_replace('\\', '/', __DIR__) . '/');

# Verbose error handling is default
// while (ob_get_level()>0) { ob_end_clean(); }
error_reporting(E_ALL);
ini_set('display_errors', 1);

# Init GDO autoloader
global $GDT_LOADED;
$GDT_LOADED = 0; # perf

spl_autoload_register(function($name) {
    if ( ($name[0]==='G') && ($name[3] === '\\') )
    {
        $name = str_replace('\\', '/', $name) . '.php';
        {
            require GDO_PATH . $name;
            # perf
            global $GDT_LOADED;
            $GDT_LOADED++;
        }
    }
});
	
######################
### Global utility ###
######################
function sitename() { return t('sitename'); }
function url($module, $method, $append='', $lang=true) { return urlSEO('index.php', $module, $method, $append, $lang); }
function urlSEO($seoString, $module, $method, $append='', $lang=true) { return GDT_Url::absolute(hrefSEO($seoString, $module, $method, $append, $lang)); }
function jxhref($module, $method, $append='', $lang=true) { return href($module, $method, $append.'&ajax=1&fmt=json', $lang); }
function href($module, $method, $append='', $lang=true)
{
    return hrefSEO($_SERVER['SCRIPT_NAME'], $module, $method, $append, $lang);
}
function hrefDefault() { return href(GDO_MODULE, GDO_METHOD); }
function hrefSEO($seoString, $module, $method, $append='', $lang=true)
{
    if (defined('GDO_SEO_URLS') && GDO_SEO_URLS)
    {
        if ($seoString === '/index.php')
        {
            $html = $seoString === $_SERVER['SCRIPT_NAME'] ? '' : '.html'; # append .html?
            $href = urlencodeSEO($seoString) . "{$html}?mo={$module}&me={$method}";
        }
        else
        {
            $href = GDO_WEB_ROOT . $_SERVER['SCRIPT_NAME'] . "?mo={$module}&me={$method}";
        }
    }
    else
    {
        $href = GDO_WEB_ROOT . $_SERVER['SCRIPT_NAME'] . "?mo={$module}&me={$method}";
    }
    
    if ($lang)
    {
        $href .= '&_lang='.Trans::$ISO;
    }
    
    $href .= $append;

    return $href;
}
function urlencodeSEO($str) { return trim(preg_replace('#[^\\.\\p{L}0-9]#', '_', $str), '_'); }

function quote($value) { return GDO::quoteS($value); }
function json_quote($s) { return str_replace("'", "&#39;", $s); }
function html($html)
{
    return Application::instance()->isCLI() ?
        $html :
        str_replace(['&', '"', "'", '<', '>'],
            ['&amp;', '&quot;', '&#39;', '&lt;', '&gt;'], $html);
}
function env($key, $default=null) { return Env::get($key, $default); }
function def($key, $default=null) { return defined($key) ? constant($key) : $default; }
function hdr($header, $replace=null)
{
    $app = Application::instance();
    if ($app->isUnitTests())
    {
        echo $header . PHP_EOL;
    }
    elseif (!$app->isCLI())
    {
        header($header, $replace);
    }
}

#######################
### Translation API ###
#######################

/**
 * Global translate function to translate into current language ISO.
 * @param string $key
 * @param array $args
 * @return string
 */
function t($key, array $args=null) { return Trans::t($key, $args); }

/**
 * Global translate function to translate into english.
 * @see t()
 * @param string $key
 * @param array $args
 * @return string
 */
function ten($key, array $args=null) { return Trans::tiso('en', $key, $args); }

/**
 * Global translate function to translate into an ISO language code.
 * @param string $iso
 * @param string $key
 * @param array $args
 * @return string
 */
function tiso($iso, $key, array $args=null) { return Trans::tiso($iso, $key, $args); }

/**
 * Global translate function to translate into a user's language.
 * @param GDO_User $user
 * @param string $key
 * @param array $args
 * @return string
 */
function tusr(GDO_User $user, $key, array $args=null) { return Trans::tiso($user->getLangISO(), $key, $args); }

/**
 * Display a date value into current language iso.
 * @param string $date Date in DB format.
 * @param string $format format key from trans file; e.g: 'short', 'long', 'date', 'exact'.
 * @param string $default the default string to display when date is null or invalid.
 * @return string
 */
function tt($date=null, $format='short', $default='---') { return Time::displayDate($date, $format, $default); }

##################
### Method API ###
##################
/**
 * @deprecated - Use GDO\\Module\\Method\\Class::make() instead
 * @param string $module
 * @param string $method
 * @return \GDO\Core\Method
 */
function method($moduleName, $methodName)
{
    $klass = "GDO\\$moduleName\\Method\\$methodName";
    return new $klass();
}

/**
 * Get requested module name.
 * @return string
 */
function mo() { return Common::getRequestString('mo', GDO_MODULE); }

/**
 * Get requested method name
 * @return string
 */
function me() { return Common::getRequestString('me', GDO_METHOD); }

/**
 * Check if a module is enabled.
 * @param string $moduleName
 * @return boolean
 */
function module_enabled($moduleName)
{
    $module = ModuleLoader::instance()->getModule($moduleName);
    return $module ? $module->isEnabled() : false;
}
