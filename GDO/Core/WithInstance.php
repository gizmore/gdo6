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
			self::$INSTANCE = new self;
		}
		return self::$INSTANCE;
	}
	
}
