<?php
namespace GDO\Core;

use GDO\DB\Cache;
use GDO\File\FileUtil;
use GDO\File\Filewalker;
use GDO\Language\Trans;
use GDO\DB\Database;
use GDO\Table\Sort;

/**
 * Module loader.
 * Can load from DB and/or FS.
 * Uses memcached for fast modulecache loading.
 *
 * @author gizmore
 * @version 6.11.2
 * @since 3.0.0
 */
final class ModuleLoader
{
	/**
	 * @return ModuleLoader
	 */
	public static function instance() { return self::$INSTANCE; }
	private static $INSTANCE;
	
	/**
	 * Base modules path, the modules folder.
	 * @var string
	 */
	private $path;
	public function __construct($path)
	{
		$this->path = $path;
		self::$INSTANCE = $this;
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
	public function &getModules()
	{
		return $this->modules;
	}
	
	/**
	 * @var GDO_Module[]
	 */
	public static $ENABLED_MODULES = null;
	
	/**
	 * Get all enabled and loaded modules.
	 * @return GDO_Module[]
	 */
	public function getEnabledModules()
	{
	    if (self::$ENABLED_MODULES === null)
	    {
	        $enabled = array_filter($this->modules, function(GDO_Module $module) {
    			return $module->isEnabled();
    		});
	        self::$ENABLED_MODULES = &$enabled;
	    }
	    return self::$ENABLED_MODULES;
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
	public function getModule($moduleName, $fs=false, $throw=true)
	{
	    $moduleName = strtolower($moduleName);
	    if (isset($this->modules[$moduleName]))
	    {
	        return $this->modules[$moduleName];
	    }
	    return $fs ? $this->loadModuleFS($moduleName, $throw) : false;
	}
	
	/**
	 * Get a module by ID.
	 * @return GDO_Module
	 */
	public function getModuleByID($moduleID)
	{
	    $moduleID = (string) $moduleID;
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
		# Register themes
		# Load language
		foreach ($this->getEnabledModules() as $module)
		{
	        $module->onLoadLanguage();
	        if ($theme = $module->getTheme())
	        {
	            GDT_Template::registerTheme($theme, $module->filePath("thm/$theme/"));
	        }
		}
		Trans::inited(true);
	}
		
	public function initModulesB()
	{
		# Init modules
		$app = Application::instance();
		if (!$app->isInstall())
		{
			foreach ($this->getEnabledModules() as $module)
			{
				if (!$module->isInited())
				{
					$module->onInit();
					$module->initedModule();
				}
			}
		}
	}
	
	public function onIncludeScripts()
	{
		foreach ($this->getEnabledModules() as $module)
		{
			$module->onIncludeScripts();
		}
	}
	
	##################
	### Massloader ###
	##################
	private $loadedDB = false;
	private $loadedFS = false;
	
	public function loadModulesA()
	{
		$hasdb = !!GDO_DB_ENABLED;
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
		    $this->loadedDB = false;
		    $this->loadedFS = false;
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
// 		    $init = !$loadDB;
			$this->loadModulesFS(false);
			$loaded = $this->loadedFS = true;
		}
		
		# Loaded one?
		if ($loaded)
		{
// 		    if ( (!Application::instance()->isInstall()) ||
// 		         ($this->loadedDB) )
// 		    {
    			$this->initModuleVars();
// 		    }

			$this->modules = $this->sortModules([
			    'module_priority' => true,
				'module_name' => true
			]);
			
			$this->initModules();
		}
		return $this->modules;
	}
	
	private function loadModulesDB()
	{
	    if (!GDO_DB_ENABLED)
	    {
	        return false;
	    }
		try
		{
			$result = GDO_Module::table()->select()->exec();
			while ($moduleData = $result->fetchAssoc())
			{
				$moduleName = strtolower($moduleData['module_name']);
				if (!isset($this->modules[$moduleName]))
				{
					try
					{
						if ($module = self::instanciate($moduleData))
						{
							$this->modules[$moduleName] = $module->setPersisted(true);
						}
					}
					catch (\Throwable $e)
					{
						echo "A module file or folder is missing in filesystem: GDO/{$moduleName}(\n";
					}
				}
			}
			return $this->modules;
		}
		catch (\GDO\DB\DBException $e)
		{
		    if (Application::instance()->isCLI())
		    {
    		    echo "The table gdo_module does not exist yet.\n";
				echo "You can ignore this error if you are using the CLI installer.\n";
		    }
		    return false;
		}
		catch (\Throwable $e)
		{
		    Logger::logException($e);
			return false;
		}
	}
	
	private function loadModulesFS($init=true)
	{
// 	    Trans::inited(false);
		Filewalker::traverse($this->path, null, false, array($this, '_loadModuleFS'), false, $init);
		Trans::inited(true);
		$this->sortModules(['module_priority' => true]);
		foreach ($this->modules as $module)
		{
			if ($init)
			{
				$module->buildConfigCache();
				$module->buildSettingsCache();
			}
		}
	}
	
	public function _loadModuleFS($entry, $path, $init)
	{
		if (FileUtil::isFile("$path/Module_$entry.php"))
		{
			$this->loadModuleFS($entry, $init);
		}
	}
	
	/**
	 * Load a module from filesystem if it is not loaded yet.
	 * @param string $name The case sensitive name.
	 * @param boolean $throw If it shall throw an exception if not found.
	 * @return \GDO\Core\GDO_Module
	 */
	public function loadModuleFS($name, $throw=true)
	{
	    $lowerName = strtolower($name);
		if (!isset($this->modules[$lowerName]))
		{
			$className = "GDO\\$name\\Module_$name";
			if (@class_exists($className, true))
			{
				$moduleData = GDO_Module::table()->blankData(['module_name' => $name]);
				if ($module = self::instanciate($moduleData, true))
				{
					$this->modules[$lowerName] = $module;
				    $module->onLoadLanguage();
				    if ($theme = $module->getTheme())
				    {
				        GDT_Template::registerTheme($theme, $module->filePath("thm/$theme/"));
				    }
// 				    if (!Application::instance()->isInstall())
// 				    {
//     				    $module->buildConfigCache();
//     				    $module->buildSettingsCache();
// 				    }
				}
			}
			elseif ($throw)
			{
				throw new GDOError('err_module', [html($name)]);
			}
			else
			{
			    return null;
			}
		}
		return $this->modules[$lowerName];
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
		/** @var $instance GDO_Module **/
		if (class_exists($klass))
		{
    		$instance = new $klass();
    		$instance->isTable = false;
    		$moduleData['module_priority'] = $instance->module_priority;
    		$instance->setGDOVars($moduleData, $dirty);
    		return $instance;
		}
	}
	
	############
	### Vars ###
	############
	/**
	 * Load module vars from database.
	 */
	public function initModuleVars()
	{
	    foreach ($this->modules as $module)
	    {
	        $module->buildConfigCache();
	    }
	    
		# Query all module vars
		try
		{
		    if (Database::instance())
		    {
    		    $result = GDO_ModuleVar::table()->
        			select('module_name, mv_name, mv_value')->
    		    	exec();
        		# Assign them to the modules
        		while ($row = $result->fetchRow())
        		{
        		    /** @var $module \GDO\Core\GDO_Module **/
        			if ($module = @$this->modules[strtolower($row[0])])
        			{
        				if ($gdt = $module->getConfigColumn($row[1], false))
        				{
        				    $gdt->initial($row[2]);
        				}
        			}
        		}
		    }
		}
		catch (\GDO\DB\DBException $e)
		{
		    if (Application::instance()->isCLI())
		    {
		        echo "No database available yet...\n";
		    }
		}
		catch (\Throwable $e)
		{
		    Logger::logException($e);
		}
		
		foreach ($this->modules as $module)
		{
    		$module->buildSettingsCache();
		}
	}
	
	public function sortModules(array $orders)
	{
	    Sort::sortArray($this->modules, GDO_Module::table(), $orders);
	    return $this->modules;
	}
	
}
