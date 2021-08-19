<?php
namespace GDO\DB;
use GDO\Core\GDOError;

final class DBException extends GDOError
{
	public function __construct($key, array $args=null, $code=500)
	{
		parent::__construct($key, $args, $code);
	}

}
