<?php
namespace GDO\DB;

/**
 * An enum without internationalization. For example Used in GDT_FontWeight and JQueryUI Theme selector. 
 * 
 * @author gizmore
 * @version 6.10
 * @since 6.10
 * 
 * @see GDT_Enum
 */
class GDT_EnumNoI18n extends GDT_Enum
{
	public function renderCell() { return $this->getVar(); }
	public function enumLabel($enumValue=null) { return $enumValue; }
	
}
