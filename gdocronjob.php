<?php
use GDO\Core\Application;
use GDO\DB\Database;
use GDO\Core\Logger;
use GDO\Cronjob\Cronjob;
use GDO\Language\Trans;
use GDO\Core\Debug;
use GDO\CLI\CLI;

############
### Init ###
############
if (php_sapi_name() !== 'cli')
{
    echo "This is a CLI application.";
    die(-1);
}

require 'GDO6.php';
require 'protected/config.php';

CLI::setServerVars();
Debug::init();
Logger::init();
Database::init();
Trans::setISO('en');

final class gdocronjob extends Application
{
    public function isCLI() { return true; }
    public function isCronjob() { return true; }
}

new gdocronjob();
/** @var $argv string[] **/
$force = in_array('--force', $argv, true);
Cronjob::run($force);
