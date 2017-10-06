<?php
namespace GDO\DB;
class GDT_Char extends GDT_String
{
	public $encoding = self::ASCII;
	public $caseSensitive = true;
	
	public function size($size)
	{
		$this->min = $this->max = $size;
		return $this;
	}

	public function gdoColumnDefine()
	{
	    $collate = $this->gdoCollateDefine($this->caseSensitive);
		return
		"{$this->identifier()} CHAR({$this->max}) CHARSET {$this->gdoCharsetDefine()} {$collate}" .
		$this->gdoNullDefine() . $this->gdoInitialDefine();
	}
}
