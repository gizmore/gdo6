<?php
namespace GDO\DB;
/**
 * Named identifier.
 * Is unique among their table and case-s ascii.
 * @author gizmore
 * @version 6.05
 * @since 6.01
 */
class GDT_Name extends GDT_String
{
	public function plugVar() { return 'Name'; }
	public function defaultLabel() { return $this->label('name'); }

	const LENGTH = 64;
	
	public $min = 2, $max = self::LENGTH;
	public $encoding = self::ASCII;
	public $caseSensitive = true;
	public $pattern = "/^[A-Za-z][-a-zA-Z_0-9.]{1,63}$/i";
	public $notNull = true;
	public $unique = true;
	
}
