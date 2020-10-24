<?php
namespace GDO\Core\Test;

use PHPUnit\Framework\TestCase;
use GDO\Core\ModuleLoader;
use GDO\DB\Result;

final class GDOSortTest extends TestCase
{
    public function testGDOSorting()
    {
        $modules = ModuleLoader::instance()->loadModules(false, true);
    }
    
}
