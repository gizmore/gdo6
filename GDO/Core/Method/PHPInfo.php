<?php
namespace GDO\Core\Method;
use GDO\Core\Method;

final class PHPInfo extends Method
{
	public function execute()
	{
		phpinfo();
		die();
	}
}
