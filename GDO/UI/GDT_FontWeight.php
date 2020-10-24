<?php
namespace GDO\UI;

use GDO\DB\GDT_Enum;

/**
 * Font weight enum select.
 * @author gizmore
 * @version 6.10
 * @since 6.10
 */
class GDT_FontWeight extends GDT_Enum
{
    public $icon = 'font';
    
	public function defaultLabel() { return $this->label('font_weight'); }
	
	public function __construct()
	{
	    $this->enumValues('100', '300', '400', '500', '700', '900');
	}
	
	public function enumLabel($enumValue=null)
	{
	    return $enumValue === null ? t($this->emptyLabel, $this->emptyLabelArgs) : $enumValue;
	}
	
}
