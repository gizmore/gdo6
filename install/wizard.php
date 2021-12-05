<?php
namespace install;
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

final class wizard extends Application
{
    public function isInstall() { return true; }
    public function getThemes() { return ['install', 'default']; }
}
new wizard();

# Current user is ghost
GDO_User::setCurrent(GDO_User::ghost());

GDT_Page::make();

# Load only two basic modules from FS for installation process.
ModuleLoader::instance()->loadModuleFS('Core');
ModuleLoader::instance()->loadModuleFS('Install');
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
catch (\Throwable $ex)
{
    $response = GDT_Error::responseException($ex);
}

# Render Page
$top = '';
if (Website::$TOP_RESPONSE)
{
    $top = Website::$TOP_RESPONSE->render();
}
echo GDT_Page::make()->html($top . $response->render())->render();
