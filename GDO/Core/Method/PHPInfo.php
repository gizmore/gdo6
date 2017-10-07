<?php
namespace GDO\GWF\Method;
use GDO\Core\Method;

final class PHPInfo extends Method
{
	public function execute()
	{
		phpinfo();
		die();
	}
}
