<?php
namespace GDO\Core\Method;

use GDO\Core\Method;
use GDO\Core\Website;
use GDO\Core\Module_Core;

final class GetCookie extends Method
{
	public function execute()
	{
		$json =  Module_Core::instance()->gdoUserJSON();
		Website::renderJSON($json);
	}
	
}
