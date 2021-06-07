<?php
/**
 * This prints all non-core-dependencies for a all modules.
 * The list can be copied by gdo6 authors to Core/ModuleProviders.php
 */

use GDO\DB\Database;
use GDO\Language\Trans;
use GDO\Session\GDO_Session;
use GDO\Core\Debug;
use GDO\Core\Logger;
use GDO\Core\ModuleLoader;
use GDO\Core\Application;
use GDO\Core\GDO_Module;

# Use gdo6 core
include "GDO6.php";
include "protected/config.php";

Database::init();
new ModuleLoader(GDO_PATH . 'GDO/');
GDO_Session::init(GDO_SESS_NAME, GDO_SESS_DOMAIN, GDO_SESS_TIME, !GDO_SESS_JS, GDO_SESS_HTTPS);
new Application();

# Bootstrap
Trans::$ISO = GDO_LANGUAGE;
Logger::init(null, GDO_ERROR_LEVEL); # 1st init as guest
Debug::init();
Debug::enableErrorHandler();
Debug::enableExceptionHandler();
Debug::setDieOnError(GDO_ERROR_DIE);
Debug::setMailOnError(GDO_ERROR_MAIL);

$modules = ModuleLoader::instance()->loadModules(false, true, true);

usort($modules, function(GDO_Module $m1, GDO_Module $m2) {
    return strcasecmp($m1->getName(), $m2->getName());
});

foreach ($modules as $module)
{
    $deps = $module->getDependencies();
    
    if ($deps)
    {
        $deps = '[\'' . implode("', '", $deps) . '\']';
    }
    else
    {
        $deps = '[]';
    }
    
    echo "'" . $module->getName() . "' => " . $deps . ",\n";
}
