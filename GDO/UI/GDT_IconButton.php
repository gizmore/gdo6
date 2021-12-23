<?php
namespace GDO\UI;
use GDO\Core\GDT_Template;

class GDT_IconButton extends GDT_Button
{
	public function defaultLabel() { return $this; }
	
	public function renderCell()
	{
	    return GDT_Template::php('UI', 'cell/iconbutton.php', [
	        'field' => $this,
	        'href' => $this->gdoHREF()]);
	}
	
	public function htmlDisabled()
	{
		if ($this->writable && $this->href)
		{
			return '';
		}
		return ' disabled="disabled"';
	}
	
}
