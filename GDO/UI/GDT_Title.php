<?php
namespace GDO\UI;

use GDO\DB\GDT_String;

/**
 * A short utf8 title.
 * Pretty common.
 * @author gizmore
 */
class GDT_Title extends GDT_String
{
    use WithTitle;
    
	public function defaultLabel() { return $this->label('title'); }
	
	public $min = 3;
	public $max = 64;
	
	public $icon = 'title';
	
	public function renderCell()
	{
	    $text = $this->titleEscaped ? html($this->var) : $this->var;
	    return '<span class="gdt-title">' . $text. '</span>'; 
	}
	
}
