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
		foreach ($modules as $module)
		{
			self::installModule($module);
		}
	}
	
	public static function installModule(GDO_Module $module, $reinstall=false)
	{
		self::installModuleClasses($module, $reinstall);
		
		if (!$module->isPersisted())
		{
			GDO_Module::table()->deleteWhere('module_name = '.$module->quoted('module_name'));
			$module->setVars(['module_enabled'=>'1', 'module_version'=>'6.10', 'module_priority' => $module->module_priority]);
			$module->insert();
			self::upgradeTo($module, '6.10');
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
	
	public static function installModuleClass(GDO $gdo, $reinstall=false)
	{
		if ($gdo->gdoIsTable())
		{
			$gdo->createTable($reinstall);
		}
	}
	
	public static function dropModule(GDO_Module $module)
	{
		$db = Database::instance();
		try
		{
			$db->queryWrite('SET FOREIGN_KEY_CHECKS=0');
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
			$db->queryWrite('SET FOREIGN_KEY_CHECKS=1');
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
		$version = self::increaseVersion($module);
		self::upgradeTo($module, $version);
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
						$temptable = "TEMP_{$tablename}";
						$db->disableForeignKeyCheck();
						$gdo->createTable(); # CREATE TABLE IF NOT EXIST
						$db->disableForeignKeyCheck();
						$query = "CREATE TABLE $temptable LIKE $tablename";
						$db->queryWrite($query);
						$query = "INSERT INTO $temptable SELECT * FROM $tablename";
						$db->queryWrite($query);
						$db->disableForeignKeyCheck();
						$query = "DROP TABLE $tablename";
						$db->queryWrite($query);
						$gdo->createTable(); # RECREATE TABLE
						$db->disableForeignKeyCheck();
						$columns = [];
						foreach ($gdo->gdoColumnsCache() as $gdt)
						{
							if ($c = $gdt->gdoColumnNames())
							{
								$columns = array_merge($columns, $c);
							}
						}
						$columns = implode(', ', $columns);
						$query = "INSERT INTO $tablename ($columns) SELECT $columns FROM $temptable";
						$db->queryWrite($query);
						$query = "DROP TABLE $temptable";
						$db->queryWrite($query);
					}
				}
			}
			finally
			{
				$db->enableForeignKeyCheck();
			}
		}
	}
	
	public static function includeUpgradeFile(GDO_Module $module, $version)
	{
		$upgradeFile = $module->filePath("upgrade/$version.php");
		if (FileUtil::isFile($upgradeFile))
		{
			include($upgradeFile);
		}
	}
	
	public static function increaseVersion(GDO_Module $module)
	{
		$v = sprintf('%.02f', (floatval($module->getVersion()) + 0.01));
		$module->saveVar('module_version', $v);
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
