<?php
namespace GDO\Core;

trait WithInstance
{
	private static $INSTANCE;
	public static function instance()
	{
		if (!self::$INSTANCE)
		{
			self::$INSTANCE = new self();
		}
		return self::$INSTANCE;
	}
}
