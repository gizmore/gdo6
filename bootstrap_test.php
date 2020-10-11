<?php
require 'vendor/autoload.php';

use GDO\Core\Application;
use GDO\Core\Debug;
use GDO\Core\Logger;
use GDO\Language\Trans;
use GDO\User\GDO_User;
use GDO\User\GDO_Session;
use GDO\DB\Database;
use GDO\Core\ModuleLoader;

require 'protected/config_unit_test.php';
require 'GDO6.php';
// GDO_Session::init(GWF_SESS_NAME, GWF_SESS_DOMAIN, GWF_SESS_TIME, !GWF_SESS_JS, GWF_SESS_HTTPS);

# Bootstrap
// new Application();
// Trans::$ISO = GWF_LANGUAGE;
// Logger::init(null, GWF_ERROR_LEVEL); # 1st init as guest
// Debug::init();
// Debug::enableErrorHandler();
// Debug::enableExceptionHandler();
// Debug::setDieOnError(false);
// Debug::setMailOnError(false);
// Database::init();
// ModuleLoader::instance()->loadModulesCache();
// GDO_Session::instance();
// if (GDO_User::current()->isAuthenticated())
// {
//     Logger::init(GDO_User::current()->getUserName(), GWF_ERROR_LEVEL); # 2nd init with username
// }

// # All fine!
// define('GWF_CORE_STABLE', 1);
