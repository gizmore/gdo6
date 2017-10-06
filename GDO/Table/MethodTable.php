<?php
namespace GDO\Table;

use GDO\Core\Method;
use GDO\DB\ArrayResult;
use GDO\Core\GDT;
use GDO\Util\Common;
/**
 * A method that displays a table.
 * 
 * @author gizmore
 * @version 5.0
 * @since 3.0
 */
abstract class MethodTable extends Method
{
	public function ipp() { return Module_Table::instance()->cfgItemsPerPage(); }
	public function isOrdered() { return true; }
	public function isFiltered() { return true; }
	public function isPaginated() { return true; }
	
	################
	### Abstract ###
	################
	/**
	 * @return GDT
	 */
	public abstract function getHeaders();
	
	/**
	 * @return ArrayResult
	 */
	public abstract function getResult();
	
	public function createTable(GDT_Table $table) {}

	###############
	### Execute ###
	###############
	public function execute()
	{
		return $this->renderTable();
	}
	
	public function renderTable()
	{
		$table = GDT_Table::make();
		$table->addFields($this->getHeaders());
		$this->createTable($table);
		$table->ordered($this->isOrdered());
		$table->filtered($this->isFiltered());
		$table->paginate($this->isPaginated(), $this->ipp());
		
		$result = $this->getResult();
		foreach (array_reverse(Common::getRequestArray('o'), true) as $name => $asc)
		{
			if ($gdoType = $table->getField($name))
			{
				$result->data = $gdoType->sort($result->data, !!$asc);
			}
		}
		$result->data = array_values($result->data);
		$table->result($result);
		return $table->render();
	}
}
