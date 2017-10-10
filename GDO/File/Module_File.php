<?php
namespace GDO\File;
use GDO\Core\GDO_Module;
use GDO\Core\Module_Core;

final class Module_File extends GDO_Module
{
    public $module_priority = 10;
    public function getClasses()
    {
        return array(
            'GDO\File\GDO_File',
        );
    }
    
    public function onIncludeScripts()
    {
    	$min = Module_Core::instance()->cfgMinifyJS() === 'no' ? '' : '.min';
    	$this->addBowerJavascript("flow.js/dist/flow$min.js");
    }
}
