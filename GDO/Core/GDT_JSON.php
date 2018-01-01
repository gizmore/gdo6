<?php
namespace GDO\Core;
use GDO\DB\GDT_Text;
/**
 * Datatype that uses JSON encoding to store arbitrary data.
 * @author gizmore
 * @see \GDO\User\GDO_Session
 * @version 6.05
 */
class GDT_JSON extends GDT_Text
{
	public static function encode($data) { return json_encode($data); }
	public static function decode($string) { return json_decode($string); }

	public $editable = false;
	public $writable = false;
	public $caseSensitive = true;
	
	public function toVar($value) { return $value === null ? null : self::encode($value); }
	public function toValue($var) { return $var === null ? null : self::decode($var); }

	public function renderJSON() { return (array)$this->toValue($this->getVar()); }
}
