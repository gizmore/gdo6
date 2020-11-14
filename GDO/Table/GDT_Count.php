<?php
namespace GDO\Table;
use GDO\Core\GDT;
use GDO\UI\WithLabel;

/**
 * Simple row number counter++
 * @author gizmore
 */
class GDT_Count extends GDT
{
	use WithLabel;
	
	public $virtual = true;
	
	public $orderable = false;
	
	public function defaultLabel() { return $this; }
	
	private $num = 1;
	public function renderCell()
	{
		return $this->num++;
	}

}
