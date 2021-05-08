<?php
namespace GDO\Table;

use GDO\Core\GDT_Template;
use GDO\DB\GDT_Checkbox;

/**
 * Can be first element in a @link GDO_Table to show checkmark selection.
 * Table header is select-all-tristate.
 * @author gizmore
 */
final class GDT_RowNum extends GDT_Checkbox
{
	public $multiple = true;
	
	public $orderable = false;
	
	public $name = 'rbx';
	public function name($name=null) { return $this; }
	
	public $num = 0;
	
	public $toggleAll = false;
	public function toggleAll($toggleAll)
	{
		$this->toggleAll = $toggleAll;
		return $this;
	}
	
	###############################
	### Different filter header ###
	###############################
	public function displayHeaderLabel() { return ''; }

	public function renderHeader()
	{
		return GDT_Template::php('Table', 'filter/rownum.php', ['field'=>$this]);
	}
	
	public function renderCell()
	{
		return GDT_Template::php('Table', 'cell/rownum.php', ['field'=>$this]);
	}
	
}
