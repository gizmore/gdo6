<?php
namespace GDO\Core\Method;

use GDO\Core\MethodAjax;
use GDO\Core\ModuleLoader;
use GDO\Core\GDT;

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
                $settings[$module->getName()][] = $this->gdtSetting($gdt);
            }
        }
        die(json_encode($settings, JSON_PRETTY_PRINT));
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
