<?php
namespace GDO\UI;
use GDO\Core\GDT;
use GDO\Core\GDT_Template;
/**
 * A simple text string.
 * 
 * @author gizmore
 *
 */
class GDT_Label extends GDT
{
	use WithLabel;
	public function renderCell()
	{
		return GDT_Template::php('UI', 'cell/label.php', ['field'=>$this]);
	}
	
	public function renderJSON()
	{
		return array(
			$this->name => $this->displayLabel(),
		);
	}
}
