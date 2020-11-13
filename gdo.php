<?php
use GDO\Core\Application;
use GDO\Core\Debug;
use GDO\Core\Logger;
use GDO\Core\ModuleLoader;
use GDO\DB\Database;
use GDO\Language\Trans;
use GDO\Install\Method\Configure;
use GDO\Admin\Method\Install;
use GDO\Core\GDO_ModuleVar;
use GDO\Form\MethodForm;
use GDO\User\GDO_User;
use GDO\Util\BCrypt;
use GDO\Net\GDT_IP;
use GDO\User\GDO_Permission;
use GDO\User\GDO_UserPermission;

/** @var $argc int **/
/** @var $argv array **/

function printUsage()
{
    global $argv;
    $exe = $argv[0];
    echo "Usage: php $exe configure <filename.php>\n";
    echo "Usage: php $exe install <module>\n";
    echo "Usage: php $exe admin <username> <password>\n";
    echo "Usage: php $exe wipe <module>\n";
    echo "Usage: php $exe config <module>\n";
    echo "Usage: php $exe config <module> <key>\n";
    echo "Usage: php $exe config <module> <key> <var>\n";
    echo "Usage: php $exe call <module> <method> <json_get_params> <json_form_params>\n";
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
ModuleLoader::instance()->loadModules(true, true);

// ModuleLoader::instance()->loadModuleFS('Install', true);

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
    $deps = $module->dependencies();
    $deps[] = $module->getName();
    $cnt = 0;
    while ($cnt !== count($deps))
    {
        $cnt = count($deps);
        foreach ($deps as $dep)
        {
            $depmod = ModuleLoader::instance()->getModule($dep);
            
            $deps = array_unique(array_merge($depmod->dependencies(), $deps));
        }
    }
    
    $deps2 = [];
    foreach ($deps as $moduleName)
    {
        $mod = ModuleLoader::instance()->getModule($moduleName);
        $deps2[$moduleName] = $mod->module_priority;
    }
    asort($deps2);
    
    echo t('msg_installing_modules', [implode(', ', array_keys($deps2))]) . "\n";
    
    foreach (array_keys($deps2) as $depmod)
    {
        $response = Install::make()->requestParameters(['module' => $depmod])->formParametersWithButton([], 'install')->execute();
        if ($response->isError())
        {
            echo json_encode($response->renderJSON(), JSON_PRETTY_PRINT);
        }
        
    }
}

if ($argv[1] === 'admin')
{
    if ($argc !== 4)
    {
        printUsage();
    }
    if (!($user = GDO_User::table()->getBy('user_name', $argv[2])))
    {
        $user = GDO_User::blank([
            'user_name' => $argv[2],
            'user_type' => GDO_User::MEMBER,
        ])->insert();
    }
    $user->saveVar('user_password', BCrypt::create($argv[3])->__toString());
    GDO_UserPermission::grant($user, 'admin');
    GDO_UserPermission::grant($user, 'staff');
    GDO_UserPermission::grant($user, 'cronjob');
    $user->recache();
    echo t('msg_admin_created', [$argv[2]]) . "\n";
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
