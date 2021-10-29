<?php
namespace GDO\Core;

use GDO\Install\Installer;
use GDO\DB\Database;
use GDO\Date\Time;
use GDO\Cronjob\MethodCronjob;
use GDO\User\GDO_User;
use GDO\Cronjob\GDO_Cronjob;


/**
 * Convinience cronjob launcher.
 * @TODO move to module Cronjob
 * 
 * @author gizmore
 * @version 6.10.4
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
	    GDO_User::setCurrent(GDO_User::system());
		$modules = ModuleLoader::instance()->loadModulesCache();
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
		    if (self::shouldRun($method))
		    {
		        self::executeCronjob($method);
		    }
		}
	}
	
	private static function shouldRun(MethodCronjob $method)
	{
	    $gdo = GDO_Cronjob::table();
	    $query = $gdo->select('cron_finished')
	       ->where('cron_method='.quote(get_class($method)))
	       ->order("cron_finished DESC");
	    $last = $query->exec()->fetchValue();
	    return $last ?
	       $method->runEvery() <= Time::getAgo($last) :
	       true;
	}

	public static function executeCronjob(MethodCronjob $method)
	{
		try
		{
		    $job = GDO_Cronjob::blank([
		        'cron_method' => get_class($method),
		    ])->insert();
			$db = Database::instance();
			$db->transactionBegin();
			$method->execute();
			$job->saveVars([
			    'cron_finished' => Time::getDate(),
			    'cron_success' => '1',
			]);
			$db->transactionEnd();
		}
		catch (\Exception $ex)
		{
		    if (isset($db))
		    {
		        $db->transactionRollback();
		    }
			throw $ex;
		}
	}
	
}
