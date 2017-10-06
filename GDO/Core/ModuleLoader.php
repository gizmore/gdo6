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
 * @version 7.00
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
    /**
     * @var GDO_Module[]
     */
    private $modules = [];
    
    private $loadedFS = false;
    
    public function __construct($path)
    {
        $this->path = $path;
        self::$instance = $this;
    }
    
    /**
     * Get all loaded modules.
     * @return GDO_Module[]
     */
    public function getModules()
    {
        return $this->modules;
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
    
    /**
     * Load active modules, preferably from cache.
     * Sorted by priority to be spinlock free.
     * @return GDO_Module[]
     */
    public function loadModulesCache()
    {
        if (false === ($cache = Cache::get('gdo_modules')))
        {
            $cache = $this->loadModules(true);
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
        }
        foreach ($this->modules as $module)
        {
            $module->initModule();
        }
        Trans::inited();
    }
    
    /**
     * @param $loadDB
     * @param $loadFS
     * @return \GDO\Core\GDO_Module[]
     */
    public function loadModules($loadDB=true, $loadFS=false)
    {
        $loaded = false;
        if ($loadDB)
        {
            $this->loadModulesDB();
            $loaded = true;
        }
        if ( ($loadFS) && (!$this->loadedFS) )
        {
            $this->loadModulesFS();
            $this->loadedFS = $loaded = true;
        }
        if ($loaded)
        {
            $this->sortModules('module_priority');
            if ($loadDB)
            {
                $this->initModuleVars();
            }
            $this->initModules();
            $this->sortModules('module_sort');
        }
        return $this->modules;
    }
    
    public function loadModulesDB()
    {
        $result = GDO_Module::table()->select('*')->order('module_priority')->exec();
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
        }
        return $this->modules;
    }
    
    public function loadModulesFS()
    {
        Filewalker::traverse($this->path, false, array($this, '_loadModuleFS'), false);
    }
    
    public function _loadModuleFS($entry, $path)
    {
        if (FileUtil::isFile("$path/Module_$entry.php"))
        {
            $this->loadModuleFS($entry);
        }
    }
    
    public function loadModuleFS($name, $init=false)
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
                        $module->initModule();
                        $module->registerThemes();
                    }
                }
            }
        }
        return @$this->modules[$name];
    }
    
    public static function instanciate(array $moduleData, $dirty = null)
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
        return $instance;
    }
    
    ############
    ### Vars ###
    ############
    public function initModuleVars()
    {
        $result = GDO_ModuleVar::table()->select('module_name, mv_name, mv_value')->join('LEFT JOIN gdo_module ON module_id=mv_module_id')->exec();
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
