<?php
namespace GDO\File;
use GDO\Core\GDO_Module;

final class Module_File extends GDO_Module
{
    public $module_priority = 10;
    public function getClasses()
    {
        return array(
            'GDO\File\GDO_File',
        );
    }
}
