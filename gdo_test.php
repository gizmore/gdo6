<?php
use GDO\DB\Database;
use GDO\Install\Installer;
use GDO\Core\Logger;
use PHPUnit\TextUI\Command;
use GDO\Core\Application;
use GDO\Session\GDO_Session;

if (PHP_SAPI !== 'cli') { die('Tests can only be run from the command line.'); }

require 'vendor/autoload.php';
require 'protected/config_unit_test.php';
require 'GDO6.php';
Logger::init();
Database::init();
GDO_Session::init();

$app = new Application();

echo "Dropping Test Database: ".GWF_DB_NAME.".\n";
Database::instance()->queryWrite("DROP DATABASE " . GWF_DB_NAME);
Database::instance()->queryWrite("CREATE DATABASE " . GWF_DB_NAME);
Database::instance()->useDatabase(GWF_DB_NAME);

echo "Loading modules from filesystem.\n";
$modules = $app->loader->loadModules(false, true);

foreach ($modules as $module)
{
//     echo "Installing {$module->getName()}.\n";
    Installer::installModule($module);
    
    $testDir = $module->filePath('Test');
    if (is_dir($testDir))
    {
        echo "Running tests for {$module->getName()}.\n";
        $command = new Command();
        $command->run(['phpunit', $testDir], false);
        echo "Done.\n";
    }
}
