<?php
namespace GDO\Core\Method;
use GDO\Core\Method;

class TestError extends Method
{
	public function execute()
	{
		return $this->error('err_test', ['test']);
	}
}