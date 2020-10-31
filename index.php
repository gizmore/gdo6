<?php
use GDO\Core\Application;
use GDO\Core\Debug;
use GDO\Core\Logger;
use GDO\Language\Trans;
use GDO\UI\GDT_Page;
use GDO\User\GDO_User;
use GDO\Session\GDO_Session;
use GDO\DB\Database;
use GDO\Core\ModuleLoader;
use GDO\Core\GDT_Response;
use GDO\Core\Website;
use GDO\UI\GDT_HTMLPage;

@include 'protected/config.php';
if (!defined('GWF_CONFIGURED'))
{
    echo "<!DOCTYPE html><html><body><h1>GDO6</h1><p>Please create a config.php, preferrably with <a href=\"install/wizard.php\">the install wizard.</a></p></body></html>\n";
    die();
}

include 'GDO6.php';
GDO_Session::init(GWF_SESS_NAME, GWF_SESS_DOMAIN, GWF_SESS_TIME, !GWF_SESS_JS, GWF_SESS_HTTPS);

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
ModuleLoader::instance()->loadModulesCache();
GDO_Session::instance();
if (GDO_User::current()->isAuthenticated())
{
	Logger::init(GDO_User::current()->getUserName(), GWF_ERROR_LEVEL); # 2nd init with username
}

$page = GDT_Page::make('page');

# All fine!
define('GWF_CORE_STABLE', 1);
try
{
	$rqmethod = $_SERVER['REQUEST_METHOD'];
	if (!in_array($rqmethod, ['GET', 'POST'], true))
	{
		die('METHOD not processed: ' . $rqmethod);
	}

	# Exec
    ob_start();
    $method = $app->getMethod();
    $response = $method->exec();
	if ($session = GDO_Session::instance())
    {
        $session->commit();
    }
}
catch (Exception $e)
{
	Logger::logException($e);
    $response = GDT_Response::makeWithHTML(Debug::backtraceException($e))->code(405);
}
finally
{
    $content = ob_get_contents();
    ob_end_clean();
}

# Render Page
switch ($app->getFormat())
{
    case 'json':
    	Website::renderJSON($response->renderJSON());
        
    case 'html':
        if ($app->isAjax())
        {
            die($response->renderHTML());
        }
        else
        {
            echo $page->html($content . $response->renderHTML())->render();
        }
}
