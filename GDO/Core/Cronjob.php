<?php
namespace GDO\Core;

use GDO\Install\Installer;
use GDO\DB\Database;
use GDO\Cronjob\MethodCronjob;


/**
 * Convinience cronjob launcher.
 * 
 * @author gizmore
 * @version 6.05
 * @see MethodCronjob
 */
final class Cronjob
{
	/**
	 * Cronjobs main.
	 * Loop over all enabled modules to run cronjob.
	 */
	public static function run()
	{
		$modules = ModuleLoader::instance()->loadModules();
		foreach ($modules as $module)
		{
			if ($module->isEnabled())
			{
				Installer::loopMethods($module, [
					__CLASS__,
					'runCronjob'
				]);
			}
		}
	}

	/**
	 * Path traversal entry point. Method is encoded in $entry
	 * @param string $entry
	 * @param string $path
	 * @param \GDO\Core\GDO_Module $module
	 */
	public static function runCronjob($entry, $path, $module)
	{
		$method = Installer::loopMethod($module, $path);
		if ($method instanceof MethodCronjob)
		{
			self::executeCronjob($method);
		}
	}

	public static function executeCronjob(MethodCronjob $method)
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
