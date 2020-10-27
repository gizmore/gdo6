<?php
namespace GDO\Core;

use GDO\DB\GDT_ObjectSelect;

final class GDT_Module extends GDT_ObjectSelect
{
    public function __construct()
    {
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
    
}
