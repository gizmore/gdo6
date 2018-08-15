<?php
namespace GDO\Install\Method;
use GDO\Core\Method;
class Welcome extends Method
{
	public function execute()
	{
		return $this->templatePHP('page/welcome.php');
	}
}
