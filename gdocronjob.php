<?php
use GDO\Core\Application;
use GDO\DB\Database;
use GDO\Core\Logger;
use GDO\Cronjob\Cronjob;

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

Logger::init();
Database::init();

final class CronjobApplication extends Application
{
    public function isCLI() { return true; }
    public function isCronjob() { return true; }
    
}

new CronjobApplication();
Cronjob::run();
