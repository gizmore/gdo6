<?php
namespace GDO\Core;

use GDO\DB\Cache;
use GDO\File\FileUtil;
use GDO\File\Filewalker;
use GDO\Language\Trans;
use GDO\DB\Database;

/**
 * Module loader.
 * Can load from DB and/or FS.
 * Uses memcached for fast modulecache loading.
 *
 * @author gizmore
 * @version 6.10.1
 * @since 3.0.0
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
	public function getModule($moduleName)
	{
	    $moduleName = strtolower($moduleName);
		return isset($this->modules[$moduleName]) ? 
		  $this->modules[$moduleName] : null;
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
// 	private $loadCached = false;
	public function loadModulesCache()
	{
	    $this->loadCached = true;
	    
		if (false === ($cache = Cache::get('gdo_modules')))
		{
			$cache = $this->loadModulesA();
			Cache::set('gdo_modules', $cache);
		}
		else
		{
// 		    Cache::heat('gdo_modules', $cache); # is not gdoCached.
			$this->initFromCache($cache);
		}
		
// 		$this->loadCached = false;
		return $this->modules;
	}
	
	private function initFromCache(array $cache)
	{
		$this->modules = $cache;
		$this->initModules();
	}
	
	public function initModules()
	{
		# Block certain modules
		foreach ($this->modules as $module)
		{
	        $module->onLoadLanguage();
		    
	        if ($theme = $module->getTheme())
	        {
	            GDT_Template::registerTheme($theme, $module->filePath("thm/$theme/"));
		    }
		    
		    if (!$module->isBlocked())
		    {
    			if ($blocked = $module->getBlockedModules())
    			{
    			    foreach ($blocked as $moduleName)
    			    {
    			        if ($blockedModule = $this->getModule($moduleName))
    			        {
    			            $blockedModule->setBlocked();
    			        }
    			    }
    			}
		    }
		}
		
		Trans::inited(true);
		
		$app = Application::instance();
		if ( (!$app->isInstall()) && (!$app->isCLI()) )
		{
			foreach ($this->modules as $module)
			{
			    if (!$module->isBlocked())
			    {
    				if ($module->isEnabled())
    				{
    					if (!$module->isInited())
    					{
    					    $module->onInit();
    					    $module->onIncludeScripts();
    						$module->initedModule();
    					}
    				}
			    }
			}
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
		    $init = !$loadDB;
			$this->loadModulesFS($init);
			$loaded = $this->loadedFS = true;
		}
		
		# Loaded one?
		if ($loaded)
		{
// 			if ($this->loadedDB)
			{
				$this->initModuleVars();
			}

			$this->modules = $this->sortModules('module_priority');
			
			$this->initModules();
		}
		return $this->modules;
	}
	
	private function loadModulesDB()
	{
		try
		{
			$result = GDO_Module::table()->select('*')->exec();
			while ($moduleData = $result->fetchAssoc())
			{
				$moduleName = strtolower($moduleData['module_name']);
				if (!isset($this->modules[$moduleName]))
				{
					if ($module = self::instanciate($moduleData))
					{
						$this->modules[$moduleName] = $module->setPersisted(true);
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
	    Trans::inited(false);
		Filewalker::traverse($this->path, null, false, array($this, '_loadModuleFS'), false, $init);
		Trans::inited(true);
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
	 * @param string $name
	 * @param boolean $init
	 * @return \GDO\Core\GDO_Module
	 */
	public function loadModuleFS($name, $init=true)
	{
	    $lowerName = strtolower($name);
		if (!isset($this->modules[$lowerName]))
		{
			$className = "GDO\\$name\\Module_$name";
			if (class_exists($className))
			{
				$moduleData = GDO_Module::table()->blankData(['module_name' => $name]);
				if ($module = self::instanciate($moduleData, true))
				{
					$this->modules[$lowerName] = $module;
// 					if ($init)
					{
					    $module->buildConfigCache();
					    $module->buildSettingsCache();
					    $module->onLoadLanguage();
					    if ($theme = $module->getTheme())
					    {
					        GDT_Template::registerTheme($theme, $module->filePath("thm/$theme/"));
					    }
					}
				}
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
        			join('JOIN gdo_module ON module_id=mv_module_id')->exec();
        		# Assign them to the modules
        		while ($row = $result->fetchRow())
        		{
        		    /** @var $module \GDO\Core\GDO_Module **/
        			if ($module = $this->modules[strtolower($row[0])])
        			{
        				if ($gdt = $module->getConfigColumn($row[1]))
        				{
        				    $gdt->initial($row[2]); #->var($row[2]);
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
	
	public function sortModules($columnName, $ascending=true)
	{
		return GDO_Module::table()->sort($this->modules, $columnName, $ascending);
	}
	
}
