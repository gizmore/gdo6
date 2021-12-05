<?php
namespace bin;

/**
 * Execute gdo6 methods via CLI.
 * We assume everything is installed correctly. If an exception is thrown display it, send mails, etc :)
 *  - gizmore - the gdo project.
 * @version 6.11.1
 * @since 6.4.0
 */
use GDO\CLI\CLI;
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
use GDO\Core\Website;

if (PHP_SAPI !== 'cli')
{
    echo 'The console has to be used for this program.' . PHP_EOL;
    die(1);
}

function printUsage($exitCode=0)
{
    printf("Usage: \$ gdo <module> to show module methods.\n");
    printf("Usage: \$ gdo <module.method> to print method help.\n");
    printf("Usage: \$ gdo <module.method.> to execute the submit button. (dot after method)\n");
    printf("Usage: \$ gdo <module.method> <args> to execute the submit button. (dot after method can be ommited)\n");
    printf("Usage: \$ gdo <module.method.button> to execute a different button than submit.\n");
	printf("\n");
	printf("Parameters: paramters are like: \$ gdo account.form \"--real_name=Christian Busch\" --language=de --timezone=Europe/Berlin\n");
	printf("Parameters: positional parameters are required and default null. They have to be specified after optionals. Positional Parameters may not use/require --param_name=\n");
	printf("\n");
	printf("Example: \$ gdo mail.send giz \"Hi there!\" \"Here goes the mail body!\"\n");
	die($exitCode);
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

# Early logger init with system user
Logger::init('system', GDO_ERROR_LEVEL);

###################
### Application ###
###################
/**
 * Execute gdo6 methods via CLI.
 *
 * @see Method
 *
 * @author gizmore
 * @version 6.11.1
 * @since 6.0.1
 */
final class gdo extends Application
{
    private $cli = true;
    public function cli($cli) { $this->cli = $cli; return $this; }
    public function isCLI() { return $this->cli; }
    public function isUnitTests() { return false; }
}

$app = new gdo();

############
### Init ###
############
Debug::init();
Debug::setMailOnError(GDO_ERROR_MAIL);
Debug::setDieOnError(GDO_ERROR_DIE);
Debug::enableErrorHandler();
Debug::enableExceptionHandler();
Debug::$MAX_ARG_LEN = 60;
Cache::init();
Database::init();
GDO_Session::init(GDO_SESS_NAME, GDO_SESS_DOMAIN, GDO_SESS_TIME, !GDO_SESS_JS, GDO_SESS_HTTPS);

$app->loader->loadModulesCache();

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

# Switch logger to user file
Logger::init($user->getUserName(), GDO_ERROR_LEVEL);

$page = GDT_Page::make();

/** @var $argc int **/
/** @var $argv string[] **/

if (GDO_LOG_REQUEST)
{
	Logger::log('cli', json_encode($argv));
}

if (!module_enabled('CLI'))
{
	echo t('err_module_disabled', ['CLI']) . "\n";
	die(-1);
}

$app->initThemes();
$app->loader->initModulesB();

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


# Do the repl or execute a single command.
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
        	echo Debug::debugException($ex);
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
	echo Debug::debugException($ex);
}
finally
{
    @fclose($stdin);
}

die (GDT_Response::globalError() ? 1 : 0);
