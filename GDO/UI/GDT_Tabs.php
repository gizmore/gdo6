<?php
namespace GDO\UI;
use GDO\Core\GDT_Template;
use GDO\Core\GDT;

/**
 * A navigation tab menu.
 * @author gizmore
 *
 */
final class GDT_Tabs extends GDT
{
	/**
	 * @var GDT_Tab[]
	 */
	private $tabs = [];
	public function getTabs()
	{
		return $this->tabs;
	}

	public function tab(GDT_Tab $tab)
	{
		$this->tabs[] = $tab;
		return $this;
	}
	
	public function getFields()
	{
	    return $this->tabs;
	}
	
	##############
	### Render ###
	##############
	public function renderForm()
	{
		return GDT_Template::php('UI', 'cell/tabs.php', ['field' => $this, 'cell' => false]);
	}
	
	public function renderCell()
	{
	    return GDT_Template::php('UI', 'cell/tabs.php', ['field' => $this, 'cell' => true]);
	}
}
