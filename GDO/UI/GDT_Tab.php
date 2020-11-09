<?php
namespace GDO\UI;

use GDO\Core\GDT_Template;
use GDO\Core\GDT;
use GDO\Core\WithFields;

/**
 * A tab panel.
 * 
 * @author gizmore
 * @version 6.10
 * @since 6.02
 */
final class GDT_Tab extends GDT
{
	use WithLabel;
	use WithFields;

	##############
	### Render ###
	##############
	public function renderForm()
	{
		return GDT_Template::php('UI', 'cell/tab.php', ['field' => $this, 'cell' => false]);
	}
	
	public function renderCell()
	{
		return GDT_Template::php('UI', 'cell/tab.php', ['field' => $this, 'cell' => true]);
	}

}
