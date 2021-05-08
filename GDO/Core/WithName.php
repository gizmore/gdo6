<?php
namespace GDO\Core;

/**
 * Adds naming conventions to a GDT.
 * 
 * @author gizmore
 * @version 6.10.3
 * @since 6.0.0
 */
trait WithName
{
    /**
     * Translate this gdo table's name via Trans.
     * @return string
     */
	public function gdoHumanName() { return t(strtolower(self::gdoShortNameS())); }
	public function gdoClassName() { return static::class; }
	public function gdoShortName() { $r = new \ReflectionClass($this); return $r->getShortName(); }
// 	public function gdoShortName() { return substr(strrchr(get_class($this), '\\'), 1); }
	public static function gdoClassNameS() { return static::class; }
	public static function gdoShortNameS() { $r = new \ReflectionClass(static::class); return $r->getShortName(); }
	
}
