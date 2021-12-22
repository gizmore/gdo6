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
 * @version 6.10.4
 * @since 6.2.0
 */
class GDT_Title extends GDT_String
{
    use WithTitle;
    
	public function defaultLabel() { return $this->label('title'); }
	
	public $min = 2;
	public $max = 128;
	public $icon = 'title';
	public $notNull = true;
	public $encoding = self::UTF8;
	public $caseSensitive = false;
	
	public function renderCell()
	{
	    $text = $this->renderTitle();
	    $text = $this->titleEscaped ? html($text) : $text;
	    return '<h3 class="gdt-title">' . $text . '</h3>'; 
	}
	
	public function renderCLI()
	{
	    return $this->displayLabel() . ': ' .
	       $this->renderTitle();
	}
	
	public function var($var=null)
	{
	    $this->titleRaw = $var;
	    return parent::var($var);
	}

}
