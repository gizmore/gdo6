<?php
namespace GDO\Core\Method;

use GDO\Core\Method;
use GDO\Core\Module_Core;
use GDO\UI\GDT_Label;
use GDO\UI\GDT_Container;

/**
 * Get gdo and php version.
 *  
 * @author gizmore
 * @version 6.11.4
 */
final class Version extends Method
{
	public function getTitle()
	{
		return t('version');
	}

	public function execute()
	{
		return GDT_Container::make('version')->addFields([
			GDT_Label::make('php')->labelRaw(PHP_VERSION),
			GDT_Label::make('gdo')->labelRaw(Module_Core::GDO_REVISION),
		]);
	}
	
}
