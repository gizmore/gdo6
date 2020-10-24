<?php
namespace GDO\Table;

use GDO\Core\Method;
use GDO\DB\Query;
use GDO\Core\GDT;
use GDO\Core\GDT_Response;

/**
 * A method that displays a table.
 * 
 * @author gizmore
 * @version 5.0
 * @since 3.0
 */
abstract class MethodQueryTable extends Method
{
	public function ipp() { return Module_Table::instance()->cfgItemsPerPage(); }
	public function isOrdered() { return true; }
	public function isFiltered() { return true; }
	public function isPaginated() { return true; }
	
	################
	### Abstract ###
	################
	/**
	 * @return Query
	 */
	public abstract function getQuery();
	
	public function getCountQuery()
	{
	    return $this->getQuery()->copy()->selectOnly('COUNT(*)');
	}
	
	/**
	 * @return GDT[]
	 */
	public function getHeaders()
	{
		return $this->getQuery()->table->gdoColumns();
	}
	
	public function onDecorateTable(GDT_Table $table) {}
	
	############
	### Exec ###
	############
	public function execute()
	{
		$table = GDT_Table::make('table');
		$table->addHeaders($this->getHeaders());
		$table->query($this->getQuery());
		$table->countQuery($this->getCountQuery());
		$table->gdo($table->query->table);
		$table->ordered($this->isOrdered());
		$table->filtered($this->isFiltered());
		$table->paginate($this->isPaginated(), $_SERVER['REQUEST_URI'], $this->ipp());
		$this->onDecorateTable($table);
		return GDT_Response::makeWith($table);
	}

}
