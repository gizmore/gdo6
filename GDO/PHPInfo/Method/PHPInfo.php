<?php
namespace GDO\PHPInfo\Method;
use GDO\Core\Method;

final class PHPInfo extends Method
{
    public function getPermission() { return 'staff'; }
    
	public function execute()
	{
		phpinfo();
		die();
	}
}
