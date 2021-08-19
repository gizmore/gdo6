<?php
namespace GDO\Core;

trait WithEvents
{
	public static $events = array();

	public function __destruct()
	{
		$this->removeEvents();
	}

	public function removeEvents()
	{

	}

	public static function onS($event, $callback)
	{

	}

	public function on($event, $callback)
	{
		self::onS($event, $callback);
	}

}
