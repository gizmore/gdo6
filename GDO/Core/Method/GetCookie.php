<?php
namespace GDO\Core\Method;

use GDO\Core\GDT_Array;
use GDO\Core\Module_Core;
use GDO\Core\MethodAjax;

final class GetCookie extends MethodAjax
{
	public function execute()
	{
		$json =  Module_Core::instance()->gdoUserJSON();
		return GDT_Array::makeWith($json);
	}
	
}
