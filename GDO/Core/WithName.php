<?php
namespace GDO\Core;

/**
 * Adds naming conventions to a GDT.
 * 
 * @author gizmore
 * @version 6.10
 * @since 6.00
 */
trait WithName
{
	public function gdoHumanName() { return t(strtolower(self::gdoShortNameS())); }
	public function gdoClassName() { return static::class; }
	public function gdoShortName() { $r = new \ReflectionClass($this); return $r->getShortName(); }
	public static function gdoClassNameS() { return static::class; }
	public static function gdoShortNameS() { $r = new \ReflectionClass(static::class); return $r->getShortName(); }
	
}
