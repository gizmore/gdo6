<?php
namespace GDO\Core;
use GDO\DB\GDT_Text;
/**
 * Datatype that uses PHP serialize to store arbitrary data.
 * Used in Session.
 * 
 * @author gizmore
 * @see \GDO\User\GDO_Session
 * @version 6.10
 * @since 5.00
 */
class GDT_Serialize extends GDT_Text
{
	public static function serialize($data) { return base64_encode(serialize($data)); }
	public static function unserialize($string) { return unserialize(base64_decode($string)); }

	public $max = 65535;

	public $editable = false;
	public $writable = false;
	public $encoding = self::ASCII;
	public $caseSensitive = true;

	public function toVar($value) { return empty($value) ? null : self::serialize($value); }
	public function toValue($var) { return $var === null ? null : self::unserialize($var); }
}
