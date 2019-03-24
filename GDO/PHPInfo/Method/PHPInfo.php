<?php
namespace GDO\PHPInfo\Method;
use GDO\Core\Method;

final class PHPInfo extends Method
{
	public function execute()
	{
		phpinfo();
		die();
	}
}
