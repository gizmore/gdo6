<?php
namespace GDO\Perf;
use GDO\UI\GDT_Bar;
use GDO\Core\GDO_Module;

/**
 * Performance statistics in footer.
 * @author gizmore
 */
final class Module_Perf extends GDO_Module
{
	public function hookBottomBar(GDT_Bar $bottomBar)
	{
		$bottomBar->addField(GDT_PerfBar::make());
	}
}
