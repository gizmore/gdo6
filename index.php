<?php
use GDO\Core\Application;
use GDO\Core\Debug;
use GDO\Core\Logger;
use GDO\Language\Trans;
use GDO\UI\GDT_Page;
use GDO\User\GDO_User;
use GDO\User\GDO_Session;
use GDO\DB\Database;
use GDO\Util\Common;
use GDO\Core\ModuleLoader;
use GDO\Core\GDT_Error;
use GDO\Core\GDT_Response;

@include 'protected/config.php';
if (!defined('GWF_CONFIGURED'))
{
    echo "<!doctype html><html><body><h1>GDO6</h1><p>Please create a config.php, preferrably with <a href=\"install/wizard.php\">the install wizard.</a></p></body></html>\n";
    die();
}

include 'GDO6.php';

# Bootstrap
$app = new Application();
Trans::$ISO = GWF_LANGUAGE;
Logger::init(null, GWF_ERROR_LEVEL); # 1st init as guest
Debug::init();
Debug::enableErrorHandler();
Debug::enableExceptionHandler();
Debug::setDieOnError(GWF_ERROR_DIE);
Debug::setMailOnError(GWF_ERROR_MAIL);
Database::init();
GDO_Session::init(GWF_SESS_NAME, GWF_SESS_DOMAIN, GWF_SESS_TIME, !GWF_SESS_JS, GWF_SESS_HTTPS);
ModuleLoader::instance()->loadModulesCache();
GDO_Session::instance();
Logger::init(GDO_User::current()->getUserName(), GWF_ERROR_LEVEL); # 2nd init with username

# All fine!
define('GWF_CORE_STABLE', 1);
try
{
    # Exec
    ob_start();
    $method = method(Common::getGetString('mo', GWF_MODULE), Common::getGetString('me', GWF_METHOD));
    $response = $method->exec();
}
catch (Exception $e)
{
	Logger::logException($e);
    $response = GDT_Response::makeWithHTML(Debug::backtraceException($e));
}
finally
{
    $content = ob_get_contents();
    ob_end_clean();
}

# Render Page
switch (Application::instance()->getFormat())
{
    case 'json':
        die(json_encode($response->render()));
        
    case 'html':
        if (Application::instance()->isAjax())
        {
            die($response->render());
        }
        else
        {
            echo GDT_Page::make()->html($content . $response->render())->render();
        }
}
