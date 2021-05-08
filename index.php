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
use GDO\Core\GDT_Error;
use GDO\Core\Method\Page404;
use GDO\Util\Strings;
use GDO\DB\Cache;
use GDO\Core\GDT_JSON;
use GDO\Core\GDT_Response;
use GDO\Core\GDT_Hook;

require 'GDO6.php';

@include 'protected/config.php';
if (!defined('GDO_CONFIGURED'))
{
    die("<!DOCTYPE html><html><body><h1>GDO6</h1><p>Please create a config.php, preferrably with <a href=\"install/wizard.php\">the install wizard.</a></p></body></html>\n");
}

GDT_Page::make();
$response = GDT_Response::make();

Database::init();
new ModuleLoader(GDO_PATH . 'GDO/');
GDO_Session::init(GDO_SESS_NAME, GDO_SESS_DOMAIN, GDO_SESS_TIME, !GDO_SESS_JS, GDO_SESS_HTTPS);
$app = new Application();

# Bootstrap
Trans::$ISO = GDO_LANGUAGE;
Logger::init(null, GDO_ERROR_LEVEL); # 1st init as guest
Debug::init();
Debug::enableErrorHandler();
Debug::enableExceptionHandler();
Debug::setDieOnError(GDO_ERROR_DIE);
Debug::setMailOnError(GDO_ERROR_MAIL);
ModuleLoader::instance()->loadModulesCache();
$session = GDO_Session::instance();
if (GDO_User::current()->isUser())
{
    if ($name = GDO_User::current()->getUserName())
    {
        $name = str_replace(GDO_User::GUEST_NAME_PREFIX, '_', $name);
    	Logger::init($name, GDO_ERROR_LEVEL); # 2nd init with username
    }
}

# All fine!
define('GDO_CORE_STABLE', 1);
try
{
	$rqmethod = $_SERVER['REQUEST_METHOD'];
	if (!in_array($rqmethod, ['GET', 'POST', 'HEAD', 'OPTIONS'], true))
	{
		die('HTTP method not processed: ' . html($rqmethod));
	}

	# Exec
    ob_start();
    if (!isset($_REQUEST['mo']))
    { 
        # If we are not index or index, and not start with a query string immediately we have a 404 error.
        $f = $_SERVER['REQUEST_URI'];
        if ( (($f !== (GDO_WEB_ROOT.'index.php')) && ($f !== '/'))
            && (!Strings::startsWith($f, '/?')) )
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

    if (GDO_DB_ENABLED && $method->isLockingSession() && $session)
    {
//         Database::instance()->lock('sess_'.$session->getID());
    }
    
    GDT_Hook::callHook('BeforeRequest', $method);
    
    $response = $method->exec();

    GDT_Hook::callHook('AfterRequest', $method);
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
        if ($response)
        {
            if ($content)
            {
                $response->addField(GDT_JSON::make()->var($content));
            }
            $response->add(Website::$TOP_RESPONSE);
            $content = Website::renderJSON($response->renderJSON());
        }
    	break;
        
    case 'html':
        if ($app->isAjax())
        {
            $content = $response->renderHTML();
        }
        else
        {
            $content = $content . $response->render(); 
        }
        
        $content = GDT_Page::$INSTANCE->html($content)->render();
        break;
        
    case 'XML':
        $content = $response->render();
        break;
}

# Save session
if ($session)
{
    $session->commit();
}

# Fire recache IPC events.
Cache::recacheHooks();

echo $content;

die(0);
