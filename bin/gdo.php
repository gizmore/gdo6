<?php
/**
 * Execute gdo6 methods via CLI.
 * 
 * @see Method
 * 
 * @author gizmore
 * @version 6.10.6
 * @since 6.0.1
 */

use GDO\Core\Logger;
use GDO\Core\Debug;
use GDO\DB\Cache;
use GDO\DB\Database;
use GDO\Date\Time;
use GDO\Session\GDO_Session;
use GDO\UI\GDT_Page;
use GDO\Core\Application;
use GDO\File\FileUtil;
use GDO\User\GDO_User;
use GDO\Language\Trans;
use GDO\Core\GDT_Response;
use GDO\Core\GDT_Error;
use GDO\Core\Website;

if (PHP_SAPI !== 'cli')
{
    echo 'The console has to be used for this program.' . PHP_EOL;
    die(1);
}


function printUsage()
{
    printf("Usage: ./gdo module to show module methods.\n");
    printf("Usage: ./gdo module.method to print help.\n");
    printf("Usage: ./gdo module.method. to execute the submit button. (dot after method)\n");
    printf("Usage: ./gdo module.method.button to execute a different button than submit.\n");
    die(0);
}

set_include_path(__DIR__ . '/../');

require 'GDO6.php';

if (FileUtil::isFile('protected/config_cli.php'))
{
    require 'protected/config_cli.php';
}
else
{
    require 'protected/config.php';
}

CLI::autoFlush();

Logger::init('system', GDO_ERROR_LEVEL);

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
Debug::setDieOnError(GDO_ERROR_DIE);
Debug::enableErrorHandler();
Debug::enableExceptionHandler();
Debug::$MAX_ARG_LEN = 60;
Cache::init();
Database::init();
GDO_Session::init(GDO_SESS_NAME, GDO_SESS_DOMAIN, GDO_SESS_TIME, !GDO_SESS_JS, GDO_SESS_HTTPS);

$app->loader->loadModules(GDO_DB_ENABLED, !GDO_DB_ENABLED, true);


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
Time::setTimezone($user->getTimezone());
Logger::init($user->getName(), GDO_ERROR_LEVEL);

$page = GDT_Page::make();

/** @var $argc int **/
/** @var $argv string[] **/

Logger::log('cli', json_encode($argv));

$norepl = false;
if ($argc > 1)
{
    array_shift($argv);
    $argv = array_map('quote', $argv);
    $line = implode(' ', $argv);
    $norepl = true;
}
else
{
    printUsage();
}

try
{
    # repl
    if (!$norepl)
    {
        $stdin = fopen('php://stdin', 'r');
    }
    do
    {
        try
        {
            # Reset vars
            $page->reset();
            $_GET = $_POST = $_REQUEST = [];
            $_REQUEST['fmt'] = 'cli';
            GDT_Response::$CODE = 200;
            
            # Exec
            if ($response = CLI::execute($line))
            {
                if (Website::$TOP_RESPONSE)
                {
                    echo Website::$TOP_RESPONSE->renderCLI() . "\n";
                }
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
    while ((!$norepl) && $line = fgets($stdin));
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
