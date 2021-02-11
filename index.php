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
use GDO\Core\Website;
use GDO\UI\GDT_Container;
use GDO\UI\GDT_HTML;
use GDO\Core\GDT_Error;
use GDO\Core\Method\Page404;

set_include_path('.');
include 'GDO6.php';

@include 'protected/config.php';
if (!defined('GWF_CONFIGURED'))
{
    echo "<!DOCTYPE html><html><body><h1>GDO6</h1><p>Please create a config.php, preferrably with <a href=\"install/wizard.php\">the install wizard.</a></p></body></html>\n";
    die();
}

$page = GDT_Page::make('page');

Database::init();
new ModuleLoader(GDO_PATH . 'GDO/');
GDO_Session::init(GWF_SESS_NAME, GWF_SESS_DOMAIN, GWF_SESS_TIME, !GWF_SESS_JS, GWF_SESS_HTTPS);
$app = new Application();

# Bootstrap
Trans::$ISO = GWF_LANGUAGE;
Logger::init(null, GWF_ERROR_LEVEL); # 1st init as guest
Debug::init();
Debug::enableErrorHandler();
Debug::enableExceptionHandler();
Debug::setDieOnError(GWF_ERROR_DIE);
Debug::setMailOnError(GWF_ERROR_MAIL);
ModuleLoader::instance()->loadModulesCache();
GDO_Session::instance();
if (GDO_User::current()->isAuthenticated())
{
    # @TODO: username can be ambigious? check if guests and members can have the same name. if so make sure the guest prefix is a valid filename on all filesystems.
	Logger::init(GDO_User::current()->getUserName(), GWF_ERROR_LEVEL); # 2nd init with username
}

# All fine!
define('GWF_CORE_STABLE', 1);
try
{
	$rqmethod = $_SERVER['REQUEST_METHOD'];
	if (!in_array($rqmethod, ['GET', 'POST', 'HEAD'], true))
	{
		die('METHOD not processed: ' . $rqmethod);
	}

	# Exec
    ob_start();
    if (!isset($_REQUEST['mo']))
    { 
        $f = $_SERVER['REQUEST_URI'];
        if ( ($f !== (GWF_WEB_ROOT.'index.php')) && ($f !== '/') )
        {
            $method = Page404::make();
            $_GET['mo'] = $_REQUEST['mo'] = 'Core';
            $_GET['me'] = $_REQUEST['me'] = 'Page404';
        }
    }
    
    if (!isset($method))
    {
        $method = $app->getMethod();
    }
    $response = $method->exec();
}
catch (Throwable $e)
{
	Logger::logException($e);
	Debug::debugException($e, false); # send exception mail
	$response = GDT_Error::responseException($e);
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
        if ($session = GDO_Session::instance())
        {
            $session->commit();
        }
        if ($content)
        {
            echo $content;
        }
        if ($response)
        {
            $response->add(Website::$TOP_RESPONSE);
            Website::renderJSON($response->renderJSON());
        }
        die(0);
    	break;
        
    case 'html':
        if ($app->isAjax())
        {
            $out = $response->renderHTML();
        }
        else
        {
            $container = GDT_Container::make('c1')->addFields([GDT_HTML::withHTML($content), $response]);
            $out = $page->html($container->renderCell())->renderCell();
        }
}

if ($session = GDO_Session::instance())
{
    $session->commit();
}

echo $out;