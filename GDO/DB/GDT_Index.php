<?php
namespace GDO\DB;
use GDO\Core\GDT;
/**
 * Index db column definition.
 * @author gizmore
 * @version 6.05
 */
class GDT_Index extends GDT
{
	use WithDatabase;
	
	private $indexColumns;
	
	public function indexColumns(...$indexColumns)
	{
		$this->indexColumns = implode(',', array_map(array('GDO\Core\GDO', 'escapeIdentifierS'), $indexColumns));
		return $this;
	}
	
	###########
	### GDT ###
	###########
	public function gdoColumnDefine()
	{
		return "INDEX({$this->indexColumns})";
	}
}
