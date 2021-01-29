<?php
namespace GDO\UI;

use GDO\DB\GDT_String;

/**
 * A short utf8 title.
 * Pretty common.
 * 
 * NotNull because if we have a title it is mandatory.
 * Also has a nice big T as default icon.
 * 
 * @author gizmore
 * 
 * @version 6.10
 * @since 6.02
 */
class GDT_Title extends GDT_String
{
    use WithTitle;
    
	public function defaultLabel() { return $this->label('title'); }
	
	public $min = 2;
	public $max = 96;
	public $icon = 'title';
	public $notNull = true;
	public $encoding = self::UTF8;
	public $caseSensitive = false;
	
	public function renderCell()
	{
	    $text = $this->renderTitle();
	    $text = $this->titleEscaped ? html($text) : $text;
	    return '<span class="gdt-title">' . $text . '</span>'; 
	}
	
	public function var($var=null)
	{
	    $this->titleRaw = $var;
	    return parent::var($var);
	}

}
