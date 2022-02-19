<?php
namespace GDO\UI;

use GDO\Core\GDT_Template;
use GDO\Core\GDT;
use GDO\Core\WithFields;

/**
 * A tab panel.
 * 
 * @author gizmore
 * @version 6.11.4
 * @since 6.0.2
 */
final class GDT_Tab extends GDT
{
	use WithLabel;
	use WithFields;
	
	private static $TABNUM = 0;
	
	public function defaultName()
	{
		return 'tab' . (++self::$TABNUM);
	}

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
