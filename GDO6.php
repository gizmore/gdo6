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
 * GDO autoloader and public functions.
 * @author gizmore
 * @version 6.10
 * @since 6.00
 */

# perf
define('GWF_PERF_START', microtime(true));

# Autoconf path
define('GDO_PATH', str_replace('\\', '/', __DIR__) . '/');
chdir(GDO_PATH);

# Verbose error handling
// while (ob_get_level()>0) { ob_end_clean(); }
error_reporting(E_ALL);
ini_set('display_errors', 1);

# Init GDO autoloader
global $GDT_LOADED;
$GDT_LOADED = 0; # perf
spl_autoload_register(function($name){
    $name = str_replace('\\', '/', $name) . '.php';
    if (file_exists($name))
    {
        require_once $name;
        global $GDT_LOADED; $GDT_LOADED++; # perf
    }
});
	
# Global utility
function sitename() { return t('sitename'); }
function url($module, $method, $append='') { return urlSEO('index.php', $module, $method, $append); }
function urlSEO($seoString, $module, $method, $append='') { return GDT_Url::absolute(hrefSEO($seoString, $module, $method, $append)); }
function href($module, $method, $append='') { return hrefSEO('index.php', $module, $method, $append); }
function hrefDefault() { return href(GWF_MODULE, GWF_METHOD); }
function hrefSEO($seoString, $module, $method, $append='')
{
    if (defined('GWF_SEO_URLS') && GWF_SEO_URLS)
    {
        $html = $seoString === 'index.php' ? '' : '.html'; # append .html?
        return GWF_WEB_ROOT . urlencodeSEO($seoString) . "{$html}?mo={$module}&me={$method}&_lang=".Trans::$ISO."$append";
    }
    else
    {
        return GWF_WEB_ROOT . "index.php?mo={$module}&me={$method}&_lang=".Trans::$ISO."$append";
    }
}
function quote($value) { return GDO::quoteS($value); }
function json_quote($s) { return str_replace("'", "&#39;", $s); }
function html($html) { return str_replace(['&', '"', "'", '<', '>'], ['&amp;', '&quot;', '&#39;', '&lt;', '&gt;'], $html); }
function mo() { return Common::getRequestString('mo', GWF_MODULE); }
function me() { return Common::getRequestString('me', GWF_METHOD); }
function module_enabled($moduleName) { return ($module = ModuleLoader::instance()->getModule($moduleName)) ? $module->isEnabled() : false; }
function env($key, $default=null) { return Env::get($key, $default); }
function def($key, $default=null) { return defined($key) ? constant($key) : $default; }
function urlencodeSEO($str) { return preg_replace('#[^\\.\\p{L}0-9]#', '_', $str); }
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
# Translation API
function t($key, array $args=null) { return Trans::t($key, $args); }
function ten($key, array $args=null) { return Trans::tiso('en', $key, $args); }
function tiso($iso, $key, array $args=null) { return Trans::tiso($iso, $key, $args); }
function tusr(GDO_User $user, $key, array $args=null) { return Trans::tiso($user->getLangISO(), $key, $args); }
function tt($date=null, $format='short', $default='---') { return Time::displayDate($date, $format, $default); }

/**
 * @deprecated - Use GDO\\Module\\Method\\Class::make() instead
 * @param string $module
 * @param string $method
 * @return \GDO\Core\Method
 */
function method($moduleName, $methodName) { $klass = "GDO\\$moduleName\\Method\\$methodName"; return new $klass(); }
