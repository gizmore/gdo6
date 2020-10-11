<?php
namespace GDO\Core\Test;

use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertContains;
use function PHPUnit\Framework\assertStringContainsString;
use function PHPUnit\Framework\assertEqualsCanonicalizing;
use GDO\Core\Application;
use GDO\Core\Module_Core;

final class ModuleTest extends TestCase
{
    public function testAlreadyLoaded()
    {
        # Modules are not cached. But this should be a unique identity as well.
        
        $loader = Application::instance()->loader;
        $mod1 = Module_Core::instance();
        $mod2 = $loader->loadModuleFS('Core');

        $this->assertTrue($mod1 === $mod2);
    }
    
}
