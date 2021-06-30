<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\WithFields;

/**
 * Very simple field that only has custom html content.
 * 
 * @see \GDO\UI\GDT_Panel
 * 
 * @author gizmore
 * 
 * @version 6.10.4
 * @since 6.7.0
 */
final class GDT_HTML extends GDT
{
	use WithFields;
	
	public static function withHTML($html)
	{
	    return self::make()->html($html);
	}
	
	############
	### HTML ###
	############
	public $html = '';
	public function html($html)
	{
	    $this->html = $html;
	    return $this;
	}
	
	##############
	### Render ###
	##############
	public function renderHTML()
	{
	    $html = $this->html;
	    foreach ($this->getFieldsRec() as $gdt)
	    {
	        $html .= $gdt->renderCell();
	    }
	    return $html;
	}
	
	public function render()
	{
	    return $this->renderCell();
	}
	
	public function renderCard()
	{
	    return "<div class=\"gdt-html\">{$this->renderHTML()}</div>";
	}
	
	public function renderCell()
	{
	    return $this->renderHTML();
	}
	
	public function renderJSON()
	{
	    return $this->renderHTML();
	}
	
	public function renderCLI()
	{
	    return $this->renderHTML();
	}

}
