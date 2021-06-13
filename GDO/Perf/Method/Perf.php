<?php
namespace GDO\Perf\Method;

use GDO\Core\Method;
use GDO\Perf\GDT_PerfBar;

/**
 * Render performance statistics.
 * @author gizmore
 */
final class Perf extends Method
{
    public function execute()
    {
        return GDT_PerfBar::make()->render();
    }
    
}
