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
	
	public static function installModule(GDO_Module $module, $reinstall=false)
	{
		self::installModuleClasses($module, $reinstall);
		
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

}
