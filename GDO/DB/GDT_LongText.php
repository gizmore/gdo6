<?php
namespace GDO\DB;

/**
 * LONGTEXT column.
 * 
 * @author gizmore
 * @see GDT_Text
 */
class GDT_LongText extends GDT_Text
{
	protected function gdoColumnDefineB()
	{
	    $collate = $this->gdoCollateDefine($this->caseSensitive);
	    return "LONGTEXT CHARSET {$this->gdoCharsetDefine()} {$collate}{$this->gdoNullDefine()}";
	}
	
}
