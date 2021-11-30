<?php
namespace GDO\Install;

use Exception;
use GDO\Core\Method;
use GDO\Core\GDO_Module;
use GDO\Core\ModuleLoader;
use GDO\DB\Cache;
use GDO\DB\Database;
use GDO\Core\GDO;
use GDO\File\FileUtil;
use GDO\File\Filewalker;
use GDO\User\GDO_Permission;
use GDO\Util\Strings;
use GDO\Core\Logger;
use GDO\Core\GDT_Error;
use GDO\Core\GDOException;
use GDO\Core\Website;
use GDO\Core\Application;
use GDO\Core\Debug;

/**
 * Install helper.
 * 
 * @author gizmore
 * @version 6.11.0
 * @since 6.0.0
 */
class Installer
{
	public static function installModules(array $modules)
	{
		/**
		 * @var $module GDO_Module
		 */
		foreach ($modules as $module)
		{
			try
			{
				self::installModule($module);
			}
			catch (\Throwable $e)
			{
				$app = Application::instance();
				if ($app->isCLI())
				{
					echo Debug::backtraceException($e, false, "Cannot install {$module->getName()}");
				}
				else
				{
					throw $e;
				}
			}
		}
	}
	
	public static function installModule(GDO_Module $module, $reinstall=false)
	{
		self::installModuleClasses($module, $reinstall);
		
		if (!$module->isPersisted())
		{
			$version = $module->module_version;
			GDO_Module::table()->deleteWhere('module_name = '.$module->quoted('module_name'));
			$module->setVars(['module_enabled'=>'1', 'module_version'=>$version, 'module_priority' => $module->module_priority]);
			$module->insert();
		}
		
		while ($module->getVersion() != $module->module_version)
		{
			self::upgrade($module);
		}
		
		self::installMethods($module);

		$module->onInstall();
		
		Cache::flush();
		Cache::fileFlush();
	}
	
	public static function installModuleClasses(GDO_Module $module, $reinstall=false)
	{
		if ($classes = $module->getClasses())
		{
			foreach ($classes as $class)
			{
				if (is_subclass_of($class, 'GDO\Core\GDO'))
				{
					$gdo = $class::table();
					$gdo instanceof GDO;
					if (!$gdo->gdoAbstract())
					{
						self::installModuleClass($gdo, $reinstall);
					}
				}
			}
		}
	}
	
	public static function installModuleClass(GDO $gdo)
	{
		if ($gdo->gdoIsTable())
		{
			$gdo->createTable();
		}
	}
	
	public static function dropModule(GDO_Module $module)
	{
		$db = Database::instance();
		try
		{
			$db->disableForeignKeyCheck();
			$module->onWipe();
			self::dropModuleClasses($module);
			$module->delete();
			Cache::remove('gdo_modules');
		}
		catch (Exception $ex)
		{
			throw $ex;
		}
		finally
		{
			$db->enableForeignKeyCheck();
		}
	}

	public static function dropModuleClasses(GDO_Module $module)
	{
		if ($classes = $module->getClasses())
		{
			foreach (array_reverse($classes) as $class)
			{
				if (is_subclass_of($class, 'GDO\\Core\\GDO'))
				{
					$gdo = $class::table();
					/** @var $gdo \GDO\Core\GDO **/
					if (!$gdo->gdoAbstract())
					{
						$gdo->dropTable();
					}
				}
			}
		}
	}
	
	public static function upgrade(GDO_Module $module)
	{
		$version = self::increaseVersion($module, false);
		self::upgradeTo($module, $version);
		self::increaseVersion($module, true);
	}
		
	/**
	 * On an upgrade we execute a possible upgrade file.
	 * We also recreate the database schema.
	 * 
	 * @param GDO_Module $module
	 * @param string $version
	 */
	public static function upgradeTo(GDO_Module $module, $version)
	{
		self::includeUpgradeFile($module, $version);
		self::recreateDatabaseSchema($module, $version);
	}
	
	/**
	 * Recreate a database schema / automigration.
	 * 
	 * @param GDO_Module $module
	 * @param string $version
	 */
	public static function recreateDatabaseSchema(GDO_Module $module, $version)
	{
		if ($classes = $module->getClasses())
		{
			try
			{
				$db = Database::instance();
				foreach ($classes as $classname)
				{
					/**
					 * @var GDO $gdo
					 */
					$gdo = $classname::table();
					if ($gdo->gdoIsTable())
					{
						$tablename = $gdo->gdoTableName();
						$temptable = "zzz_temp_{$tablename}";
						
						# create temp and copy as old
						$db->disableForeignKeyCheck();
						# Do not! drop the temp table. It might contain live data from a failed upgrade
						$query = "SHOW CREATE TABLE $tablename";
						$result = Database::instance()->queryRead($query);
						$query = mysqli_fetch_row($result)[1];
						$query = str_replace($tablename, $temptable, $query);
						$db->queryWrite($query);
						$query = "INSERT INTO $temptable SELECT * FROM $tablename";
						$db->queryWrite($query);

						# drop existing and recreate as new
						$query = "DROP TABLE $tablename";
						$db->queryWrite($query);
						$gdo->createTable(); # CREATE TABLE IF NOT EXIST
						$db->disableForeignKeyCheck();

						# calculate columns and copy back in new
						if ($columns = self::getColumnNames($gdo, $temptable))
						{
							$columns = implode(',', $columns);
							$query = "INSERT INTO $tablename ($columns) SELECT $columns FROM $temptable";
							$db->queryWrite($query);
							
// 							# drop temp after all succeded.
// 							$query = "DROP TABLE $temptable";
// 							$db->queryWrite($query);
						}
					}
				}
			}
			finally
			{
				$db->enableForeignKeyCheck();
			}
		}
	}
	
	/**
	 * Get intersecting columns of old and new format.
	 * @param GDO $gdo
	 * @param string $temptable
	 * @return array
	 */
	private static function getColumnNames(GDO $gdo, $temptable)
	{
		$db = GDO_DB_NAME;
		
		$query = "SELECT group_concat(COLUMN_NAME) FROM INFORMATION_SCHEMA.COLUMNS " .
		         "WHERE TABLE_SCHEMA = '{$db}' AND TABLE_NAME = '{$temptable}'";
		$result = Database::instance()->queryRead($query);
		$old = mysqli_fetch_array($result)[0];
		$old = explode(',', $old);
		
		$query = "SELECT group_concat(COLUMN_NAME) FROM INFORMATION_SCHEMA.COLUMNS " .
		"WHERE TABLE_SCHEMA = '{$db}' AND TABLE_NAME = '{$gdo->gdoTableName()}'";
		$result = Database::instance()->queryRead($query);
		$new = mysqli_fetch_array($result)[0];
		$new = explode(',', $new);
		
		return ($old && $new) ? 
			array_intersect($old, $new) : [];
	}
	
	public static function includeUpgradeFile(GDO_Module $module, $version)
	{
		$upgradeFile = $module->filePath("upgrade/$version.php");
		if (FileUtil::isFile($upgradeFile))
		{
			include($upgradeFile);
		}
	}
	
	private static function increaseVersion(GDO_Module $module, bool $write)
	{
		$v = sprintf('%.02f', (floatval($module->getVersion()) + 0.01));
		if ($write)
		{
			$module->saveVar('module_version', $v);
		}
		return $v;
	}
	
	public static function installMethods(GDO_Module $module)
	{
		self::loopMethods($module, array(__CLASS__, 'installMethod'));
	}
	
	public static function loopMethods(GDO_Module $module, $callback)
	{
		$dir = $module->filePath('Method');
		if (FileUtil::isDir($dir))
		{
			Filewalker::traverse($dir, null, $callback, false, false, $module);
		}
	}
	
	/**
	 * Helper to get the method for a method loop.
	 * @param GDO_Module $module
	 * @param string $path
	 * @return Method
	 * @deprecated because the naming is horrible. Also the logic is not nice.
	 */
	public static function loopMethod(GDO_Module $module, $path)
	{
		$entry = Strings::substrTo(basename($path), '.');
		$class_name = "GDO\\{$module->getName()}\\Method\\$entry";
		if (!class_exists($class_name, false))
		{
			include $path;
		}
		return $module->getMethod($entry);
	}
	
	public static function installMethod($entry, $path, GDO_Module $module)
	{
		$method = self::loopMethod($module, $path);
		if ($permission = $method->getPermission())
		{
			GDO_Permission::create($permission);
		}
	}

	/**
	 * Return all modules needed for a module.
	 * Used in gdo6-docs to generate a module list for a single module documentation output.
	 * @param String $moduleName
	 * @return GDO_Module[]
	 */
	public static function getDependencyModules($moduleName)
	{
	    $git = \GDO\Core\ModuleProviders::GIT_PROVIDER;
	    $module = ModuleLoader::instance()->getModule($moduleName);
	    $deps = $module->dependencies();
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
	                    return "Missing dependencie(s)!\n".
	                       "Please note that this list may not be complete, because missing modules might have more dependencies.\n";
	                }
	                $allResolved = false;
	                $providers = @\GDO\Core\ModuleProviders::$PROVIDERS[$dep];
	                if (!$providers)
	                {
	                    return "{$dep}: Not an official module or a typo somewhere. No Provider known.\n";
	                }
	                elseif (is_array($providers))
	                {
	                    $back = "{$dep}: Choose between multiple possible providers.\n";
	                    foreach ($providers as $provider)
	                    {
	                        $back .= sprintf("%20s: cd GDO; git clone --recursive {$git}{$provider} {$dep}; cd ..\n", $dep);
	                    }
	                    return $back;
	                }
	                else
	                {
	                    return sprintf("%20s: cd GDO; git clone --recursive {$git}{$providers} {$dep}; cd ..\n", $dep);
	                }
	                
	                continue;
	            }
	            
	            $deps = array_unique(array_merge($depmod->dependencies(), $deps));
	        }
	    }

	    $deps[] = $module->getName();
	    
	    return array_map(function($dep) { 
	        return ModuleLoader::instance()->getModule($dep);
	    }, $deps);
	}
	
}
