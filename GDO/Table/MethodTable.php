<?php
namespace GDO\Table;

use GDO\Core\Method;
use GDO\DB\ArrayResult;
use GDO\Core\GDT_Response;
use GDO\Core\GDT_Fields;
/**
 * A method that displays a table from memory ArrayResult.
 * 
 * @author gizmore
 * @version 6.08
 * @since 3.0
 */
abstract class MethodTable extends Method
{
	public function ipp() { return Module_Table::instance()->cfgItemsPerPage(); }

	public function isOrdered() { return true; }
	public function isFiltered() { return true; }
	public function isPaginated() { return true; }

	public function getOrderName() { return 'o'; }
	public function getDefaultOrder() { return null; }
	public function getDefaultOrderDir() { return true; }
	
	################
	### Abstract ###
	################
	/**
	 * @return GDT_Fields
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
		$table->addHeaders($this->getHeaders());
		$table->ordered($this->isOrdered());
		$table->filtered($this->isFiltered());
		$table->paginate($this->isPaginated(), $this->ipp());
		$this->createTable($table);
		$result = $this->getResult();
		$table->multisort($result, $this->getDefaultOrder(), $this->getDefaultOrderDir(), $this->getOrderName());
		$result->data = array_values($result->data);
		$table->result($result);
		return GDT_Response::makeWith($table);
	}
	
}
