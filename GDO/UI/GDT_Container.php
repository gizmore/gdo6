<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\WithFields;

/**
 * Simple collection of GDTs.
 * The render functions call the render function on all fields.
 * 
 * @author gizmore
 * @since 6.07
 */
final class GDT_Container extends GDT
{
	use WithFields;
	
	public function renderCell()
	{
		$back = '';
		foreach ($this->fields as $gdt)
		{
			$back .= $gdt->renderCell();
		}
		return $back;
	}

}