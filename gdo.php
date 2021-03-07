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
use GDO\User\GDO_UserPermission;
use GDO\DB\Cache;
use GDO\Install\Installer;
use GDO\Util\Strings;
use GDO\Core\GDO_Module;
use GDO\Core\GDT_Response;

/**
 * The gdo.php executable manages modules and config via the CLI.
 * 
 * @version 6.10
 * @since 6.10
 * @see gdo_update.sh
 * @see gdo_test.sh
 * @see gdo_yarn.sh
 * @see gdo_bower.sh
 */

/** @var $argc int **/
/** @var $argv array **/

/**
 * Show usage of the gdo.sh shell command.
 * @example gdo.sh install Bootstrap
 */
function printUsage($code=1)
{
    global $argv;
    $exe = $argv[0];
    echo "Usage: php $exe configure [<config.php>] - to generate a protected/config.php\n";
    echo "Usage: php $exe install <module>\n";
    echo "Usage: php $exe install_all\n";
    echo "Usage: php $exe wipe <module> - To uninstall modules\n";
//  echo "Usage: php $exe providers <module>\n"; # TODO: Show a list of providers for module dependencies.
    echo "Usage: php $exe admin <username> <password> [<email>] - to (re)set an admin account\n";
    echo "Usage: php $exe config <module>\n";
    echo "Usage: php $exe config <module> <key>\n";
    echo "Usage: php $exe config <module> <key> <var>\n";
    echo "Usage: php $exe call <module> <method> <json_get_params> <json_form_params>\n";
    die($code);
}

if ($argc === 1)
{
    printUsage(0);
}

require 'GDO6.php';

@include 'protected/config.php';

# Default config 
$_SERVER['HTTPS'] = 'off';
if (!defined('GWF_CONFIGURED'))
{
	define('GWF_DB_ENABLED', false); # CLI is DB Disabled
	define('GWF_WEB_ROOT', '/');
	\GDO\Install\Config::configure();
}

# App is CLI
final class InstallerApp extends Application
{
	public function isInstall() { return true; }
}

new InstallerApp(); # Create App
Trans::$ISO = GWF_LANGUAGE;
Logger::init(null, GWF_ERROR_LEVEL); # init without username
Debug::init();
Debug::enableErrorHandler();
Debug::enableExceptionHandler();
Debug::setDieOnError(GWF_ERROR_DIE);
Debug::setMailOnError(GWF_ERROR_MAIL);
Database::init();
Cache::flush();
ModuleLoader::instance()->loadModules(GWF_DB_ENABLED, true);

define('GWF_CORE_STABLE', 1);

if ($argv[1] === 'configure')
{
	if ($argc === 2)
	{
		$argv[2] = 'config.php'; # default config filename
	}
	
    $response = Configure::make()->requestParameters(['filename' => $argv[2]])->formParametersWithButton([], 'save_config')->execute();
    if ($response->isError())
    {
        echo json_encode($response->renderJSON(), JSON_PRETTY_PRINT);
    }
	
	echo "You should now edit this file by hand.\n";
	echo "Afterwards execute php gdo.php test config.\n";
}

elseif ($argv[1] === 'test')
{
	if (GWF_DB_ENABLED)
	{
		Database::init();
	}
	echo \GDO\Install\Method\SystemTest::make()->execute()->render();
	
	echo "Your configuration seems solid.\n";
	echo "You can now try to php gdo.php install <module>.\n";
	echo "A list of official modules is shown via php gdo.php modules.\n";
	echo "Before you can install a module, you have to clone it.\n";
	echo "Example: cd GDO; git clone --recursive https://github.com/gizmore/gdo6-session-cookie Session; cd ..\n";
	echo "Please note that a Session module is *required* and you have to choose between gdo6-session-db and gdo6-session-cookie.\n";
	echo "The session provider has to be cloned as a folder named GDO/Session/.\n";
}

elseif ($argv[1] === 'modules')
{
	echo "List of official modules\n";
	$providers = \GDO\Core\ModuleProviders::$PROVIDERS;
	$git = \GDO\Core\ModuleProviders::GIT_PROVIDER;
	foreach ($providers as $moduleName => $p)
	{
		if (!is_array($p)) $p = [$p];
		foreach ($p as $provider)
		{
			printf("%32s: cd GDO; git clone --recursive {$git}{$provider} {$moduleName}; cd ..\n", $moduleName);
		}
	}
}

elseif (Strings::startsWith($argv[1], 'install'))
{
    $mode = 1;
    if ($argv[1] === 'install')
    {
        if ($argc !== 3)
        {
            printUsage();
        }
    }
    elseif ($argv[1] === 'install_all')
    {
        $mode = 2;
        if ($argc !== 2)
        {
            printUsage();
        }
    }
			
	$git = \GDO\Core\ModuleProviders::GIT_PROVIDER;
	
	if ($mode === 1)
	{
        $module = ModuleLoader::instance()->loadModuleFS($argv[2]);
        $deps = $module->dependencies();
        $deps[] = $module->getName();
	}
	elseif ($mode === 2)
	{
	    $modules = ModuleLoader::instance()->loadModules(false, true, true);
	    $deps = array_map(function(GDO_Module $mod){
	        return $mod->getName();}, $modules);
	}
    
    $cnt = 0;
	$allResolved = true; # All required modules provided?
    while ($cnt !== count($deps))
    {
        $cnt = count($deps);
        foreach ($deps as $dep)
        {
            $depmod = ModuleLoader::instance()->getModule($dep);
			
			if (!$depmod)
			{
				if ($allResolved === true)
				{
					echo "Missing dependencie(s)!\n";
					echo "Please note that this list may not be complete, because missing modules might have more dependencies.\n";
				}
				$allResolved = false;
				$providers = @\GDO\Core\ModuleProviders::$PROVIDERS[$dep];
				if (!$providers)
				{
					echo "{$dep}: Not an official module or a typo somewhere. No Provider known.\n";
				}
				elseif (is_array($providers))
				{
					echo "{$dep}: Choose between multiple possible providers.\n";
					foreach ($providers as $provider)
					{
						printf("%20s: cd GDO; git clone --recursive {$git}{$provider} {$dep}; cd ..\n", $dep);
					}
				}
				else
				{
					printf("%20s: cd GDO; git clone --recursive {$git}{$providers} {$dep}; cd ..\n", $dep);
				}
				
				continue;
			}
            
            $deps = array_unique(array_merge($depmod->dependencies(), $deps));
        }
    }
	
	if (!$allResolved)
	{
		echo "Some modules are not provided by your current GDO/ folder.\n";
		echo "Please clone the modules like stated above.\n";
		die(2);
    }
	
    $deps2 = [];
    foreach ($deps as $moduleName)
    {
        $mod = ModuleLoader::instance()->getModule($moduleName);
        $deps2[$moduleName] = $mod->module_priority;
    }
    asort($deps2);
    
    echo t('msg_installing_modules', [implode(', ', array_keys($deps2))]) . "\n";
    
	$loader = ModuleLoader::instance();
    foreach (array_keys($deps2) as $depmod)
    {
		$module = $loader->getModule($depmod);
		echo "Installing module {$depmod}.\n";
		Installer::installModule($module);
    }
    
    Cache::flush();
	
	echo "Done.\n";
}

elseif ($argv[1] === 'admin')
{
    if (($argc !== 4) && (($argc !== 5)))
    {
        printUsage();
    }
    if (!($user = GDO_User::table()->getBy('user_name', $argv[2])))
    {
        $user = GDO_User::blank([
            'user_name' => $argv[2],
            'user_type' => GDO_User::MEMBER,
            'user_email' => $argc === 5 ? $argv[4] : null,
        ])->insert();
    }
    $user->saveVar('user_password', BCrypt::create($argv[3])->__toString());
    if ($argc === 5)
    {
        $user->saveVar('user_email', $argv[4]);
    }
    $user->saveVar('user_deleted_at', null);
    GDO_UserPermission::grant($user, 'admin');
    GDO_UserPermission::grant($user, 'staff');
    GDO_UserPermission::grant($user, 'cronjob');
    $user->recache();
    echo t('msg_admin_created', [$argv[2]]) . "\n";
    echo PHP_EOL;
}

elseif ($argv[1] === 'wipe')
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
        echo PHP_EOL;
    }
}

elseif ($argv[1] === 'config')
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
        echo PHP_EOL;
        die(0);
    }

    $key = $argv[3];
    if ($argc === 4)
    {
        $config = $module->getConfigColumn($key);
        echo t('msg_set_config', [$key, $module->getName(), $config->initial]);
        echo PHP_EOL;
        die(0);
    }
    
    $var = $argv[4];
    if ($argc === 5)
    {
        $gdt = $module->getConfigColumn($key)->var($var);
        if (!$gdt->validate($gdt->toValue($var)))
        {
            echo json_encode($gdt->configJSON());
            echo PHP_EOL;
            die(1);
        }
        $moduleVar = GDO_ModuleVar::createModuleVar($module, $gdt);
        echo t('msg_changed_config', [$gdt->displayLabel(), $module->getName(), $gdt->initial, $moduleVar->getVarValue()]);
        echo PHP_EOL;
        die(0);
    }
}

elseif ($argv[1] === 'call')
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
    
    if ($response = $method->execute())
    {
        echo json_encode($response->renderJSON(), JSON_PRETTY_PRINT);
    }
    else
    {
        echo GDT_Response::$CODE;
    }
    
    echo PHP_EOL;
}
else
{
    echo "Unknown command {$argv[1]}\n\n";
    printUsage();
}
