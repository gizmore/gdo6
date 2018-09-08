<?php
namespace GDO\Install;
use Exception;
use GDO\Core\Method;
use GDO\Core\GDO_Module;
use GDO\DB\Cache;
use GDO\DB\Database;
use GDO\Core\GDO;
use GDO\File\FileUtil;
use GDO\File\Filewalker;
use GDO\User\GDO_Permission;
use GDO\Util\Strings;
/**
 * Install helper.
 * @author gizmore
 * @since 6.00
 * @version 6.05
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
	
	public static function installModule(GDO_Module $module)
	{
		self::installModuleClasses($module);
		
		if (!$module->isPersisted())
		{
			GDO_Module::table()->deleteWhere('module_name = '.$module->quoted('module_name'))->exec();
			$module->setVars(['module_enabled'=>'1', 'module_version'=>'6.00', 'module_priority' => $module->module_priority]);
			$module->insert();
			self::upgradeTo($module, '6.00');
		}
		
		while ($module->getVersion() != $module->module_version)
		{
			self::upgrade($module);
		}
		
		self::installMethods($module);

		$module->onInstall();
		
		Cache::remove('gdo_modules');
	}
	
	public static function installModuleClasses(GDO_Module $module)
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
						self::installModuleClass($gdo);
					}
				}
			}
		}
	}
	
	public static function installModuleClass(GDO $gdo)
	{
		$gdo->createTable();
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
		
	public static function upgradeTo(GDO_Module $module, $version)
	{
		$upgradeFile = $module->filePath("upgrade/$version.php");
		if (FileUtil::isFile($upgradeFile))
		{
			include($upgradeFile);
		}
	}
	
	public static function increaseVersion(GDO_Module $module)
	{
		$v = (string) (floatval($module->getVersion()) + 0.01);
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
			Filewalker::traverse($dir, '*', $callback, false, false, $module);
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
// 		die($path."<br/>\n".GWF_PATH);
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
	
// 	#####################
// 	### GWF core util ###
// 	#####################
// 	private static $coreTables;
// 	public static function coreInclude($entry, $path, $args)
// 	{
// 		$class = Strings::substrTo($entry, '.');
// 		if (class_exists($class))
// 		{
// 			if (is_subclass_of($class, 'GDO'))
// 			{
// 				if ($table = GDO::tableFor($class))
// 				{
// 					if (!$table->gdoAbstract())
// 					{
// 						self::$coreTables[$class] = $table;
// 					}
// 				}
// 			}
// 		}
// 	}
	
// 	/**
// 	 * Get all core tables from inc folder.
// 	 * @return GDO[]
// 	 */
// 	public static function includeCoreTables()
// 	{
// 		self::$coreTables = [];
// 		Filewalker::traverse(GWF_PATH . 'inc/util/gwf', '*', [__CLASS__, 'coreInclude'], false, false);
// 		return self::$coreTables;
// 	}
	
// 	public static function installCoreTables($dropTables=false)
// 	{
// 		$tables = self::includeCoreTables();
// 		while (count($tables))
// 		{
// 			$changed = false;
// 			foreach ($tables as $classname => $table)
// 			{
// 				$skip = false; 
// 				if ($deps = $table->gdoDependencies())
// 				{
// 					foreach ($deps as $dep)
// 					{
// 						if (isset($tables[$dep]))
// 						{
// 							$skip = true;
// 							break;
// 						}
// 					}
// 				}
// 				if ($skip)
// 				{
// 					continue;
// 				}
// 				if ($dropTables)
// 				{
// 					$table->dropTable();
// 				}
// 				$table->createTable();
// 				$changed = true;
// 				unset($tables[$classname]);
// 				break;
// 			}
// 			if (!$changed)
// 			{
// 				throw new GDOError("err_gdo_dependency not met", [implode(', ', array_keys($tables))]);
// 			}
// 		}
// 	}
	
}
