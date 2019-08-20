<?php
namespace GDO\Core;

use GDO\Util\Strings;

/**
 * Adds naming conventions to a GDT.
 * 
 * @author gizmore
 * @since 6.00
 * @version 6.10
 */
trait WithName
{
	public function gdoHumanName() { return t(strtolower(self::gdoShortNameS())); }
	public function gdoClassName() { return self::gdoClassNameS(); }
	public function gdoShortName() { return self::gdoShortNameS(); }
	public static function gdoClassNameS() { return get_called_class(); }
	public static function gdoShortNameS() { return Strings::rsubstrFrom(get_called_class(), '\\'); }
	
}
