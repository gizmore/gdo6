<?php
use GDO\DB\Database;
use GDO\Install\Installer;
use GDO\Core\Logger;
use PHPUnit\TextUI\Command;
use GDO\Core\Application;
use GDO\Session\GDO_Session;
use GDO\File\FileUtil;
use GDO\UI\GDT_Page;
use GDO\Core\Debug;
use GDO\Core\GDO_Module;
use GDO\DB\Cache;
use GDO\Core\ModuleLoader;

/**
 * Launch all unit tests.
 * Unit tests should reside in <Module>/Test/FooTest.php
 */
if (PHP_SAPI !== 'cli') { die('Tests can only be run from the command line.'); }

require 'GDO6.php';
require 'protected/config_test.php';
require 'vendor/autoload.php';

Logger::init('system', GDO_ERROR_LEVEL);
Debug::init();
Debug::setMailOnError(GDO_ERROR_EMAIL);
// Debug::setDieOnError(GDO_ERROR_DIE);
Debug::enableErrorHandler();
Debug::enableExceptionHandler();
Cache::init();
Cache::flush();
Database::init();
GDO_Session::init(GDO_SESS_NAME, GDO_SESS_DOMAIN, GDO_SESS_TIME, !GDO_SESS_JS, GDO_SESS_HTTPS);

final class TestApp extends Application
{
    private $cli = false;
    public function cli($cli) { $this->cli = $cli; return $this; }
    public function isCLI() { return $this->cli; } # override CLI mode to test HTML rendering.
    /**
     * @override
     * {@inheritDoc}
     * @see \GDO\Core\Application::isUnitTests()
     */
    public function isUnitTests()
    {
        return true;
    }
}


$app = new TestApp();
GDT_Page::make();


#############################
### Simulate HTTP env a bit #
$_SERVER['SERVER_NAME'] = trim(GDO_DOMAIN, "\r\n\t .");
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['HTTP_USER_AGENT'] = 'Firefox Gecko MS Opera';
$_SERVER['REQUEST_URI'] = '/index.php?mo=' . GDO_MODULE . '&me=' . GDO_METHOD;
$_SERVER['HTTP_REFERER'] = 'http://'.GDO_DOMAIN.'/index.php';
$_SERVER['HTTP_ORIGIN'] = '127.0.0.2';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SERVER_SOFTWARE']	= 'Apache/2.4.41 (Win64) PHP/7.4.0';
$_SERVER['HTTP_HOST'] = GDO_DOMAIN;
$_SERVER['HTTPS'] = 'off';
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['QUERY_STRING'] = 'mo=' . GDO_MODULE . '&me=' . GDO_METHOD;
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'de-DE,de;q=0.9,en-US;q=0.8,en;q=0.7';
#########################################################################

/** @var $argc int **/
/** @var $argv string[] **/

echo "Dropping Test Database: ".GDO_DB_NAME.".\n";
echo "If this hangs, something is locking the db.\n";
Database::instance()->queryWrite("DROP DATABASE IF EXISTS " . GDO_DB_NAME);
Database::instance()->queryWrite("CREATE DATABASE " . GDO_DB_NAME);
Database::instance()->useDatabase(GDO_DB_NAME);

echo "Loading modules from filesystem\n";
$modules = $app->loader->loadModules(false, true);

FileUtil::removeDir(GDO_PATH . '/temp_test');

if ($argc === 2)
{
    $count = 0;
    $modules = explode(',', $argv[1]);

    if ($app->loader->loadModuleFS('Tests', false))
    {
        $modules[] = 'Tests';
    }
    else
    {
        echo "You don't have module gdo6-tests installed, which is probably required to create test users.\n";
        flush();
    }

    while ($count != count($modules))
    {
        $count = count($modules);

        foreach ($modules as $moduleName)
        {
            $module = ModuleLoader::instance()->getModule($moduleName);
            $more = Installer::getDependencyModules($moduleName);
            $more = array_map(function($m){
                return $m->getName();
            }, $more);
            $modules = array_merge($modules, $more);
            $modules[] = $module->getName();
        }
        $modules = array_unique($modules);
    }
    $modules = array_map(function($m){
        return ModuleLoader::instance()->getModule($m);
    }, $modules);

    usort($modules, function(GDO_Module $m1, GDO_Module $m2) {
        return $m1->module_priority - $m2->module_priority;
    });

    foreach ($modules as $module)
    {
        echo "Installing {$module->getName()}\n";
        Installer::installModule($module);
        runTestSuite($module);
    }
    echo "Finished.\n";
    return;
}

foreach ($modules as $module)
{
    if (!$module->isPersisted())
    {
        echo "Installing {$module->getName()}\n";
        Installer::installModule($module);
    }

    $testDir = $module->filePath('Test');
    if (FileUtil::isDir($testDir))
    {
        runTestSuite($module);
    }
}

echo "Finished.\n";

function runTestSuite(GDO_Module $module)
{
    $testDir = $module->filePath('Test');
    if (FileUtil::isDir($testDir))
    {
        echo "Verarbeite Tests für {$module->getName()}\n";
        $command = new Command();
        $command->run(['phpunit', $testDir], false);
        echo "Done with {$module->getName()}\n";
        echo "----------------------------------------\n";
    }
}
