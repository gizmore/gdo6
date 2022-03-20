<?php
namespace GDO\Core\Method;

use GDO\Core\Method;
use GDO\Core\Module_Core;
use GDO\UI\GDT_Label;

/**
 * Get gdo and php version. 
 * @author gizmore
 */
final class Version extends Method
{
	public function execute()
	{
		return GDT_Label::make()->labelRaw(Module_Core::GDO_REVISION);
	}
	
}
