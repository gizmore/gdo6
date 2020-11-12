<?php
use GDO\Date\Time;
use GDO\Language\Trans;
use GDO\User\GDO_User;
use GDO\Core\GDO;
use GDO\Net\GDT_Url;
use GDO\Util\Common;
use GDO\Core\ModuleLoader;
/**
 * GDO autoloader and public functions.
 * @author gizmore
 * @since 6.00
 * @version 6.10
 */
define('GWF_PERF_START', microtime(true));

# Autoconf path
define('GDO_PATH', str_replace('\\', '/', __DIR__) . '/');

# Verbose error handling
while (ob_get_level()>0) { ob_end_clean(); }
error_reporting(E_ALL);
ini_set('display_errors', 1);

# Init GDO autoloader
global $GDT_LOADED; $GDT_LOADED = 0; # perf
spl_autoload_register(function($name){
    $name = str_replace('\\', '/', $name) . '.php';
    include $name;
    global $GDT_LOADED; $GDT_LOADED++; # perf
});
	
# Global utility
function sitename() { return t('sitename'); }
function url($module, $method, $append='') { return GDT_Url::absolute(href($module, $method, $append)); }
function href($module, $method, $append='') { $iso = Trans::$ISO; return GWF_WEB_ROOT . "index.php?mo={$module}&me={$method}&_lang={$iso}$append"; }
function hrefDefault() { return href(GWF_MODULE, GWF_METHOD); }
function quote($value) { return GDO::quoteS($value); }
function json_quote($s) { return str_replace("'", "&#39;", $s); }
function html($html) { return str_replace(['"', "'", '<', '>'], ['\\"', '&#39;', '&lt;', '&gt;'], $html); }
function mo() { return Common::getRequestString('mo', GWF_MODULE); }
function me() { return Common::getRequestString('me', GWF_METHOD); }
function module_enabled($moduleName) { return ($module = ModuleLoader::instance()->getModule($moduleName)) ? $module->isEnabled() : false; }

# Translation API
function t($key, array $args=null) { return Trans::t($key, $args); }
function ten($key, array $args=null) { return Trans::tiso('en', $key, $args); }
function tiso($iso, $key, array $args=null) { return Trans::tiso($iso, $key, $args); }
function tusr(GDO_User $user, $key, array $args=null) { return Trans::tiso($user->getLangISO(), $key, $args); }
function tt($date=null, $format='short', $default='---') { return Time::displayDate($date, $format, $default); }

# Deprecated #
/**
 * Use GDO\\Module\\Method\\Class::make() instead
 * @param string $module
 * @param string $method
 * @return \GDO\Core\Method
 * @deprecated
 */
function method($module, $method) { $klass = "GDO\\$module\\Method\\$method"; return new $klass(); }
