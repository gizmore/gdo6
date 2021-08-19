<?php
namespace GDO\Table;

use GDO\DB\GDT_UInt;

/**
 * Simple row number counter++
 * @author gizmore
 */
class GDT_Count extends GDT_UInt
{
// 	use WithLabel;

	public $virtual = true;

	public $orderable = false;

	public function defaultLabel() { return $this; }

	private $num = 1;
	public function renderCell()
	{
		return $this->num++;
	}

	public function renderJSON()
	{
	    return $this->renderCell();
	}

}
