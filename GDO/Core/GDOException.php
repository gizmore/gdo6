<?php
namespace GDO\Core;
use Exception;
class GDOException extends Exception
{
	public function __construct($message, $code=500)
	{
		parent::__construct($message, $code);
	}
}
