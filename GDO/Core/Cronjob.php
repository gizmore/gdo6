<?php
namespace GDO\Core;
use GDO\Install\Installer;
use GDO\DB\Database;
/**
 * Convinience cronjob launcher.
 * 
 * @author gizmore
 * @version 5.0
 * 
 * @see MethodCronjob
 */
final class Cronjob
{
	public static function run()
	{
		$modules = ModuleLoader::instance()->loadModules();
		foreach ($modules as $module)
		{
			Installer::loopMethods($module, [__CLASS__, 'runCronjob']);
		}
	}
	
	public static function runCronjob($entry, $path, $module)
	{
		$method = Installer::loopMethod($module, $path);
		if ($method instanceof MethodCronjob)
		{
		    try
		    {
                $db = Database::instance();
                $db->transactionBegin();
                $method->execute();
                $db->transactionEnd();
		    }
		    catch (\Exception $ex)
		    {
		        $db->transactionRollback();
		        throw $ex;
		    }
		}
	}
}
