<?php
namespace GDO\Core;
use GDO\DB\Cache;
use GDO\File\FileUtil;
use GDO\File\Filewalker;
use GDO\Language\Trans;
/**
 * Module loader.
 *
 * @author gizmore
 * @version 6.05
 * @since 3.00
 */
final class ModuleLoader
{
	/**
	 * @return ModuleLoader
	 */
	public static function instance() { return self::$instance; }
	private static $instance;
	
	/**
	 * Base modules path, the modules folder.
	 * @var string
	 */
	private $path;
	public function __construct($path)
	{
		$this->path = $path;
		self::$instance = $this;
	}
	
	#############
	### Cache ###
	#############
	/**
	 * @var GDO_Module[]
	 */
	private $modules = [];
	
	/**
	 * Get all loaded modules.
	 * @return GDO_Module[]
	 */
	public function getModules()
	{
		return $this->modules;
	}
	
	/**
	 * Get all enabled and loaded modules.
	 * @return GDO_Module[]
	 */
	public function getEnabledModules()
	{
		return array_filter($this->modules, function(GDO_Module $module){
			return $module->isEnabled();
		});
	}
	
	/**
	 * Get all enabled and loaded modules.
	 * @return GDO_Module[]
	 */
	public function getInstallableModules()
	{
		return array_filter($this->modules, function(GDO_Module $module){
			return $module->isInstallable();
		});
	}
	
/**
	 * @param string $moduleName
	 * @return GDO_Module
	 */
	public function getModule($moduleName=null)
	{
		return @$this->modules[$moduleName];
	}
	
	/**
	 * Get a module by ID.
	 * @return GDO_Module
	 */
	public function getModuleByID($moduleID)
	{
		foreach ($this->modules as $module)
		{
			if ($module->getID() === $moduleID)
			{
				return $module;
			}
		}
	}
	
	#################
	### Cacheload ###
	#################
	/**
	 * Load active modules, preferably from cache.
	 * Sorted by priority to be spinlock free.
	 * @return GDO_Module[]
	 */
	public function loadModulesCache()
	{
		if (false === ($cache = Cache::get('gdo_modules')))
		{
			$cache = $this->loadModulesA();
			Cache::set('gdo_modules', $cache);
		}
		else
		{
			$this->initFromCache($cache);
		}
		
		return $this->modules;
	}
	
	private function initFromCache(array $cache)
	{
		$this->modules = $cache;
		$this->initModules();
	}
	
	public function initModules()
	{
		foreach ($this->modules as $module)
		{
			$module->registerThemes();
			$module->onLoadLanguage();
		}
		Trans::inited();
		foreach ($this->modules as $module)
		{
			$module->initModule();
		}
		$app = Application::instance();
		if ( (!$app->isInstall()) && (!$app->isCLI()) )
		{
			foreach ($this->modules as $module)
			{
				if (!$module->isInited())
				{
					$module->onIncludeScripts();
				}
			}
		}

		foreach ($this->modules as $module)
		{
			$module->initedModule();
		}
	}
	
	##################
	### Massloader ###
	##################
	private $loadedDB = false;
	private $loadedFS = false;
	
	public function loadModulesA()
	{
		$hasdb = GWF_DB_HOST !== null;
		return $this->loadModules($hasdb, !$hasdb);
	}
	
	/**
	 * @param $loadDB
	 * @param $loadFS
	 * @return \GDO\Core\GDO_Module[]
	 */
	public function loadModules($loadDB=true, $loadFS=false, $refresh=false)
	{
		if ($refresh)
		{
			$this->modules = [];
		}
		
		# Load maybe 0, 1 or 2 sources
		$loaded = false;
		if ($loadDB && (!$this->loadedDB) )
		{
			$this->loadedDB = $this->loadModulesDB() !== false;
			$loaded = true;
		}
		
		if ($loadFS && (!$this->loadedFS) )
		{
			$this->loadModulesFS();
			$loaded = $this->loadedFS = true;
		}
		
		# Loaded one?
		if ($loaded)
		{
			if ($this->loadedDB)
			{
				$this->initModuleVars();
			}

			$this->sortModules('module_priority');
			$this->initModules();
		}
		return $this->modules;
	}
	
	private function loadModulesDB()
	{
// 		try
// 		{
			$result = GDO_Module::table()->select('*')->exec();
			while ($moduleData = $result->fetchAssoc())
			{
				$moduleName = $moduleData['module_name'];
				if (!isset($this->modules[$moduleName]))
				{
					if ($module = self::instanciate($moduleData))
					{
						$this->modules[$moduleName] = $module->setPersisted(true);
					}
				}
// 				else
// 				{
// 					$this->modules[$moduleName] = $module->setPersisted(true);
// 				}
			}
// 		}
// 		catch (Exception $e)
// 		{
// 			return false;
// 		}
		return $this->modules;
	}
	
	private function loadModulesFS()
	{
		Filewalker::traverse($this->path, '*', false, array($this, '_loadModuleFS'), false);
	}
	
	public function _loadModuleFS($entry, $path)
	{
		if (FileUtil::isFile("$path/Module_$entry.php"))
		{
			$this->loadModuleFS($entry, false);
		}
	}
	
	/**
	 * Load a module from filesystem if it is not loaded yet.
	 * @param string $name
	 * @param boolean $init
	 * @return \GDO\Core\GDO_Module
	 */
	public function loadModuleFS($name, $init=true)
	{
		if (!isset($this->modules[$name]))
		{
			$className = "GDO\\$name\\Module_$name";
			if (class_exists($className))
			{
				$moduleData = GDO_Module::table()->blankData(['module_name' => $name]);
				if ($module = self::instanciate($moduleData, true))
				{
					$this->modules[$name] = $module;
					if ($init)
					{
						$module->registerThemes();
						$module->initModule();
					}
				}
			}
		}
		return @$this->modules[$name];
	}
	
	/**
	 * Instanciate a module from gdoVars/loaded data.
	 * @param array $moduleData
	 * @param bool $dirty
	 * @throws GDOError
	 * @return \GDO\Core\GDO_Module
	 */
	public static function instanciate(array $moduleData, $dirty = false)
	{
		$name = $moduleData['module_name'];
		$klass = "GDO\\$name\\Module_$name";
		$instance = new $klass();
		if (!$instance instanceof GDO_Module)
		{
			throw new GDOError('err_no_module', [html($name)]);
		}
		$moduleData['module_priority'] = $instance->module_priority;
		$instance->setGDOVars($moduleData, $dirty);
		$instance->onLoadLanguage();
		return $instance;
	}
	
	############
	### Vars ###
	############
	/**
	 * Load module vars from database.
	 */
	public function initModuleVars()
	{
		# Query all module vars
		$result = GDO_ModuleVar::table()->
			select('module_name, mv_name, mv_value')->
			join('LEFT JOIN gdo_module ON module_id=mv_module_id')->exec();
		# Assign them to the modules
		while ($row = $result->fetchRow())
		{
			if ($module = @$this->modules[$row[0]])
			{
				if ($var = $module->getConfigColumn($row[1]))
				{
					$var->initial($row[2]);
				}
			}
		}
	}
	
	public function sortModules($columnName, $ascending=true)
	{
		return GDO_Module::table()->sort($this->modules, $columnName, $ascending);
	}
	
}
