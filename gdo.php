<?php
use GDO\Core\Application;
use GDO\Core\Debug;
use GDO\Core\Logger;
use GDO\Core\ModuleLoader;
use GDO\DB\Cache;
use GDO\DB\Database;
use GDO\Language\Trans;
use GDO\Install\Method\Configure;
use GDO\Admin\Method\Install;
use GDO\Core\GDO_ModuleVar;
use GDO\Form\MethodForm;

/** @var $argc int **/
/** @var $argv array **/

function printUsage()
{
    echo "Usage: php gdo.php configure <filename.php>\n";
    echo "Usage: php gdo.php install <module>\n";
    echo "Usage: php gdo.php wipe <module>\n";
    echo "Usage: php gdo.php config <module>\n";
    echo "Usage: php gdo.php config <module> <key>\n";
    echo "Usage: php gdo.php config <module> <key> <var>\n";
    echo "Usage: php gdo.php call <module> <method> <json_get_params> <json_form_params>\n";
    die(0);
}

if ($argc === 1)
{
    printUsage();
}

require 'protected/config.php';

require_once 'GDO6.php';

new Application();
Trans::$ISO = GWF_LANGUAGE;
Logger::init(null, GWF_ERROR_LEVEL); # 1st init as guest
Debug::init();
Debug::enableErrorHandler();
Debug::enableExceptionHandler();
Debug::setDieOnError(GWF_ERROR_DIE);
Debug::setMailOnError(GWF_ERROR_MAIL);
Database::init();
Cache::init();
ModuleLoader::instance()->loadModulesCache();

ModuleLoader::instance()->loadModuleFS('Install', true);

define('GWF_CORE_STABLE', 1);

if ($argv[1] === 'configure')
{
    $response = Configure::make()->requestParameters(['filename' => $argv[2]])->formParametersWithButton([], 'save_config')->execute();
    if ($response->isError())
    {
        echo json_encode($response->renderJSON(), JSON_PRETTY_PRINT);
    }
}

if ($argv[1] === 'install')
{
    if ($argc !== 3)
    {
        printUsage();
    }
    $module = ModuleLoader::instance()->loadModuleFS($argv[2]);
    $deps = $module->getDependencies();
    
    $cnt = count($deps);
    foreach ($deps as $dep)
    {
        $depmod = ModuleLoader::instance()->loadModuleFS($dep);
        
        $deps = array_unique(array_merge($depmod->getDependencies(), $deps));
        
        if ($cnt === count($deps))
        {
            break;
        }
        $cnt = count($deps);
    }
    
    echo t('msg_installing_modules', [implode(', ', $deps)]) . "\n";
    
    foreach ($deps as $depmod)
    {
        $response = Install::make()->requestParameters(['module' => $depmod])->formParametersWithButton([], 'install')->execute();
        if ($response->isError())
        {
            echo json_encode($response->renderJSON(), JSON_PRETTY_PRINT);
        }
        
    }
    
}

if ($argv[1] === 'wipe')
{
    if ($argc !== 3)
    {
        printUsage();
    }
    $module = ModuleLoader::instance()->loadModuleFS($argv[2]);
    $response = Install::make()->requestParameters(['module' => $module->getName()])->formParametersWithButton([], 'uninstall')->execute();
    if ($response->isError())
    {
        echo json_encode($response->renderJSON(), JSON_PRETTY_PRINT);
    }
}

if ($argv[1] === 'config')
{
    if ( ($argc === 2) || ($argc > 5) )
    {
        printUsage();
    }
    $module = ModuleLoader::instance()->loadModuleFS($argv[2]);
    if ($argc === 3)
    {
        $config = $module->getConfigCache();
        $vars = [];
        foreach ($config as $key => $gdt)
        {
            $vars[] = $key;
        }
        $keys = implode(', ', $vars);
        $keys = $keys ? $keys : t('none');
        echo t('msg_available_config', [$module->getName(), $keys]);
        die(0);
    }

    $key = $argv[3];
    if ($argc === 4)
    {
        $config = $module->getConfigColumn($key);
        echo t('msg_set_config', [$key, $module->getName(), $config->initial]);
        die(0);
    }
    
    $var = $argv[4];
    if ($argc === 5)
    {
        $gdt = $module->getConfigColumn($key)->var($var);
        if (!$gdt->validate($gdt->toValue($var)))
        {
            echo json_encode($gdt->configJSON());
            die(1);
        }
        $moduleVar = GDO_ModuleVar::createModuleVar($module, $gdt);
        echo t('msg_changed_config', [$gdt->displayLabel(), $module->getName(), $gdt->initial, $moduleVar->getVarValue()]);
        die(0);
    }
}

if ($argv[1] === 'call')
{
    if ( ($argc !== 4) && ($argc !== 5) && ($argc !== 6) )
    {
        printUsage();
    }
    $module = ModuleLoader::instance()->loadModuleFS($argv[2]);
    $method = $module->getMethod($argv[3]);
    
    if ($argc >= 5)
    {
        $getParams = json_decode($argv[4], true);
        $method->requestParameters($getParams);
    }
    
    if ($argc === 6)
    {
        $formParams = json_decode($argv[5], true);
        if ($method instanceof MethodForm)
        {
            $method->formParameters($formParams);
        }
    }
    
    $response = $method->execute();
    echo json_encode($response->renderJSON(), JSON_PRETTY_PRINT);
}
