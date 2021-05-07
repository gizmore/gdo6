<?php
//use GDO\Core\Method;

/**
 * Execute gdo6 methods via CLI.
 * 
 * @see Method
 * 
 * @author gizmore
 * @version 6.10.2
 * @since 6.0.1
 */

use GDO\Core\Logger;
use GDO\Core\Debug;
use GDO\DB\Cache;
use GDO\DB\Database;
use GDO\Session\GDO_Session;
use GDO\UI\GDT_Page;
use GDO\Core\Application;
use GDO\File\FileUtil;
use GDO\Util\CLI;
use GDO\User\GDO_User;
use GDO\Language\Trans;
use GDO\Core\GDT_Response;
use GDO\Core\GDT_Error;

if (PHP_SAPI !== 'cli')
{
    echo 'The console has to be used for this program.' . PHP_EOL;
    die(1);
}

require 'GDO6.php';

CLI::autoFlush();

if (FileUtil::isFile('protected/config_cli.php'))
{
    require 'protected/config_cli.php';
}
else
{
    require 'protected/config.php';
}

Logger::init('system', GWF_ERROR_LEVEL);

###################
### Application ###
###################
final class GDOApplication extends Application
{
    private $cli = true;
    public function cli($cli) { $this->cli = $cli; return $this; }
    public function isCLI() { return $this->cli; }
    public function isUnitTests() { return false; }
}

$app = new GDOApplication();

############
### Init ###
############
Debug::init();
Debug::setMailOnError(false);
Debug::setDieOnError(GWF_ERROR_DIE);
Debug::enableErrorHandler();
Debug::enableExceptionHandler();
Debug::$MAX_ARG_LEN = 60;
Cache::init();
Database::init();

$app->loader->loadModules(GWF_DB_ENABLED, !GWF_DB_ENABLED, true);

if (module_enabled('Session'))
{
    GDO_Session::init();
}

#################
### Load User ###
#################
if (!($user = GDO_User::getBy('user_name', CLI::getUsername())))
{
    $user = GDO_User::blank([
        'user_name' => CLI::getUsername(),
        'user_type' => GDO_User::MEMBER,
    ])->insert();
    echo t('msg_new_user_created', [CLI::getUsername()]);
    echo PHP_EOL;
}
GDO_User::setCurrent($user);
Trans::setISO($user->getLangISO());
Logger::init($user->getName(), GWF_ERROR_LEVEL);

$page = GDT_Page::make();

/** @var $argc int **/
/** @var $argv string[] **/

# copy argv to stdin for the loop
$stdin = fopen('php://stdin', 'w');

if ($argc > 1)
{
    array_shift($argv);
    $line = implode(' ', $argv);
    $norepl = true;
}

try
{
    # repl
    $stdin = fopen('php://stdin', 'r');
    do
    {
        try
        {
            $page->reset();
            if ($response = CLI::execute($line))
            {
                echo $response->renderCLI();
            }
            else
            {
                echo GDT_Response::$CODE;
            }
        }
        catch (\Throwable $ex)
        {
            echo GDT_Error::responseException($ex)->render();
        }
        echo PHP_EOL;
        
        if (isset($norepl))
        {
            die (GDT_Response::globalError() ? 1 : 0);
        }
    }
    while ($line = fgets($stdin));
}
catch (\Throwable $ex)
{
    Logger::logException($ex);
    echo GDT_Error::responseException($ex)->render();
    echo PHP_EOL;
}
finally
{
    @fclose($stdin);
}

die (GDT_Response::globalError() ? 1 : 0);
