<?php
namespace GDO\Core;

trait WithInstance
{
	private static $INSTANCE;
	
	/**
	 * @return self
	 */
	public static function instance()
	{
		if (!self::$INSTANCE)
		{
			$klass = get_called_class();
			self::$INSTANCE = new $klass();
		}
		return self::$INSTANCE;
	}
}
