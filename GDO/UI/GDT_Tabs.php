<?php
namespace GDO\UI;
use GDO\Core\GDT_Template;
use GDO\Core\GDT;

/**
 * A navigation tab menu.
 * 
 * @author gizmore
 * @version 6.11.4
 * @since 6.2.0
 */
final class GDT_Tabs extends GDT
{
	public function defaultName()
	{
		return 'tabs';
	}
	
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
