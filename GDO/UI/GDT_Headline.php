<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * Simple HTML heading tag, like <h1>
 * Has a level 1-5 and uses WithHTML to display a non templated h tag.
 * 
 * @author gizmore
 * @since 6.07
 */
final class GDT_Headline extends GDT
{
	use WithHTML;
	
	public $level = 5;
	public function level($level) { $this->level = $level; return $this; }
	
	public function renderCell() { return sprintf('<h%1$d>%2$s</h%1$d>', $this->level, $this->html); }
	public function renderForm() { return $this->renderCell(); }
	public function renderJSON() { return ['headline' => $this->html, 'level' => $this->level]; }
	public function renderCard()
	{
	    return
	    sprintf('<h%1$d>%2$s</h%1$d>', $this->level, $this->displayLabel()).
	    $this->renderCell();
	}
	
}
