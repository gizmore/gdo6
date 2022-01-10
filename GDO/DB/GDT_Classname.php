<?php
namespace GDO\DB;

/**
 * A classname.
 * 
 * @author gizmore
 * @version 6.11.2
 * @since 6.11.2
 */
class GDT_Classname extends GDT_String
{
	public function plugVar() { return GDT_Name::class; }
	
	public function defaultLabel() { return $this->label('classname'); }

	const LENGTH = 255;
	
	public $min = 2, $max = self::LENGTH;
	public $encoding = self::ASCII;
	public $caseSensitive = true;
	public $pattern = "/^[A-Za-z][A-Za-z _0-9\\\\]{1,254}$/sD";
	
}
