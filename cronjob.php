<?php
use GDO\Core\Application;
############
### Init ###
############
if (php_sapi_name() !== 'cli')
{
    echo "This is a CLI application.";
    die(-1);
}

require 'protected/config.php';
require 'GDO6.php';

use GDO\DB\Database;
use GDO\Core\Logger;
use GDO\Core\Cronjob;
Logger::init();
Database::init();
final class CronjobApplication extends Application
{
    public function isCLI() { return true; }
}
$app = new CronjobApplication();
Cronjob::run();
