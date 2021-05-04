<?php
namespace GDO\Core\Method;

use GDO\Core\MethodAjax;
use GDO\Core\ModuleLoader;
use GDO\Core\GDT;
use GDO\Core\Website;

final class UserSettings extends MethodAjax
{
    public function execute()
    {
        $modules = ModuleLoader::instance()->getEnabledModules();
        $settings = [];
        foreach ($modules as $module)
        {
            $moduleSettings = $module->getSettingsCache();
            $settings[$module->getName()] = [];
            foreach ($moduleSettings as $gdt)
            {
                if ($gdt->isSerializable())
                {
                    $settings[$module->getName()][] = $this->gdtSetting($gdt);
                }
            }
        }
        
        Website::renderJSON($settings);
    }

    private function gdtSetting(GDT $gdt)
    {
        return [
            'name' => $gdt->name,
            'var' => $gdt->var,
            'type' => get_class($gdt),
            'config' => $gdt->configJSON(), 
        ];
    }
    
}
