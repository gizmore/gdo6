<?php
namespace GDO\Core\Method;

use GDO\Core\Website;
use GDO\Core\Module_Core;
use GDO\Core\MethodAjax;

final class GetCookie extends MethodAjax
{
	public function execute()
	{
		$json =  Module_Core::instance()->gdoUserJSON();
		Website::renderJSON($json);
	}
	
}
