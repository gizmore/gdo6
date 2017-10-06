<?php
namespace GDO\DB;
/**
 * The text gdoType exceeds the varchar.
 * It is displayed in a textarea like form field.
 * The cell rendering should be dottet.
 * 
 * @author gizmore
 *
 */
class GDT_Text extends GDT_String
{
	public function defaultLabel() { return $this->label('message'); }
	
	public $max = 4096;
	
	public function gdoColumnDefine()
	{
	    $collate = $this->gdoCollateDefine($this->caseSensitive);
		return "{$this->identifier()} TEXT({$this->max}) CHARSET {$this->gdoCharsetDefine()} {$collate}{$this->gdoNullDefine()}";
	}
}
