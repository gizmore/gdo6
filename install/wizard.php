<?php
chdir('../');
require 'GDO6.php';

# Configure by config file or autoconf
@include 'protected/config.php';

####
use GDO\Core\Application;
use GDO\UI\GDT_Page;
use GDO\Util\Common;
use GDO\Language\Trans;
use GDO\Util\Math;
use GDO\Core\GDT_Error;
use GDO\Core\ModuleLoader;
use GDO\Core\Debug;
use GDO\Core\Logger;
use GDO\User\GDO_User;
use GDO\Install\Config;
use GDO\Core\Website;

Config::configure();

Debug::enableErrorHandler();
Debug::enableExceptionHandler();
Debug::setDieOnError(true);
Debug::setMailOnError(false);

Logger::init();

Trans::$ISO = GDO_LANGUAGE;

final class InstallerApp extends Application
{
    public function isInstall() { return true; }
    public function getThemes() { return ['install', 'default']; }
}
new InstallerApp();

# Current user is ghost
GDO_User::$CURRENT = GDO_User::ghost();

GDT_Page::make();

# Load only two basic modules from FS for installation process.
ModuleLoader::instance()->loadModuleFS('Core', 1);
ModuleLoader::instance()->loadModuleFS('Install', 1);
Trans::inited(true);

define('GDO_CORE_STABLE', 1);
try
{
    # Execute Step
    $steps = Config::steps();
    $step = Math::clamp(Common::getGetInt('step'), 1, count($steps));
    $method = method('Install', $steps[$step-1]);
    $response = $method->execute();
}
catch (Exception $e)
{
    $response = GDT_Error::responseException($e);
}

# Render Page
$top = '';
if (Website::$TOP_RESPONSE)
{
    $top = Website::$TOP_RESPONSE->render();
}
echo GDT_Page::make()->html($top . $response->render())->render();
