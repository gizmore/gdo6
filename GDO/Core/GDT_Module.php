<?php
namespace GDO\Core;

use GDO\DB\GDT_ObjectSelect;

/**
 * A module select.
 * Features installed and uninstalled choices.
 * Loads module via module loader.
 * @author gizmore
 * @version 6.10
 * @since 6.02
 * @see GDO_Module
 */
final class GDT_Module extends GDT_ObjectSelect
{
    protected function __construct()
    {
        parent::__construct();
        $this->table(GDO_Module::table());
    }
    
    public $installed = true;
    public function installed($installed=true) { $this->installed = $installed; return $this; }
    
    public $uninstalled = false;
    public function uninstalled($uninstalled=true) { $this->uninstalled = $uninstalled; return $this; }
    
    public function initChoices()
    {
        if (!$this->choices)
        {
            $this->choices = [];
            
            $modules = ModuleLoader::instance()->loadModules($this->installed, $this->uninstalled);
            
            foreach ($modules as $module)
            {
                if ( ($module->isInstalled() && $this->installed) ||
                    ((!$module->isInstalled()) && $this->uninstalled) )
                {
                    $this->choices[$module->getName()] = $module->displayName();
                }
            }
        }
    }
    
    public function getValueSingle($moduleName)
    {
        return ModuleLoader::instance()->getModule($moduleName);
    }
    
    public function getValueMulti($var)
    {
        $loader = ModuleLoader::instance();
        $back = [];
        foreach (json_decode($var) as $id)
        {
            if ($object = $loader->getModule($id))
            {
                $back[$id] = $object;
            }
        }
        return $back;
    }
    
}
