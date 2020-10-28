<?php
namespace GDO\UI;

use GDO\DB\GDT_EnumNoI18n;

/**
 * Font weight enum select.
 * @author gizmore
 * @version 6.10
 * @since 6.10
 */
class GDT_FontWeight extends GDT_EnumNoI18n
{
    public $icon = 'font';
    
	public function defaultLabel() { return $this->label('font_weight'); }
	
}
