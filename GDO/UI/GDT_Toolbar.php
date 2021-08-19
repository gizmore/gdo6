<?php
namespace GDO\UI;
use GDO\Core\GDT_Template;
use GDO\Core\GDT;
use GDO\Core\WithFields;

class GDT_Toolbar extends GDT
{
	use WithFields;

	public function renderCell()
	{
		return GDT_Template::php('UI', 'cell/toolbar.php', ['field' => $this]);
	}
}
