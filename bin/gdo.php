<?php
use GDO\Core\Logger;
use GDO\Core\Debug;
use GDO\DB\Cache;
use GDO\DB\Database;
use GDO\Session\GDO_Session;
use GDO\UI\GDT_Page;
use GDO\Core\Application;
use GDO\Util\Strings;

/**
 * Launch all unit tests.
 * Unit tests should reside in <Module>/Test/FooTest.php
 */
if (PHP_SAPI !== 'cli') { die('Tests can only be run from the command line.'); }

require_once 'GDO6.php';
require_once 'protected/config.php';
// require_once 'vendor/autoload.php';

Logger::init('system', GWF_ERROR_LEVEL);
Debug::init();
Debug::setMailOnError(GWF_ERROR_EMAIL);
// Debug::setDieOnError(GWF_ERROR_DIE);
Debug::enableErrorHandler();
Debug::enableExceptionHandler();
Cache::init();
Cache::flush();
Database::init();
GDO_Session::init();

final class GDOApplication extends Application
{
    private $cli = true;
    public function cli($cli) { $this->cli = $cli; return $this; }
    public function isCLI() { return $this->cli; } # override CLI mode to test HTML rendering.
    public function isUnitTests() { return false; }
}


$app = new GDOApplication();
GDT_Page::make();


#############################
### Simulate HTTP env a bit #
$_SERVER['SERVER_NAME'] = trim(GWF_DOMAIN, "\r\n\t .");
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['HTTP_USER_AGENT'] = 'Firefox Gecko MS Opera';
$_SERVER['REQUEST_URI'] = '/index.php?mo=' . GWF_MODULE . '&me=' . GWF_METHOD;
$_SERVER['HTTP_REFERER'] = 'http://'.GWF_DOMAIN.'/index.php';
$_SERVER['HTTP_ORIGIN'] = '127.0.0.2';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SERVER_SOFTWARE']	= 'Apache/2.4.41 (Win64) PHP/7.4.0';
$_SERVER['HTTP_HOST'] = GWF_DOMAIN;
$_SERVER['HTTPS'] = 'off';
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['QUERY_STRING'] = 'mo=' . GWF_MODULE . '&me=' . GWF_METHOD;
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'de-DE,de;q=0.9,en-US;q=0.8,en;q=0.7';
#########################################################################

/** @var $argc int **/
/** @var $argv string[] **/


$app->loader->loadModules();

$modulename = $argv[1];
$methodname = Strings::substrFrom($modulename, '.');
$modulename = Strings::substrTo($modulename, '.');
$module = $app->loader->getModule($modulename);
$method = $module->getMethod($methodname);

if ($response = $method->execWrap())
{
    echo $response->render();
}
