<?php
namespace GDO\Table;

use GDO\DB\Query;
use GDO\Core\GDOException;

/**
 * A method that displays a table.
 * 
 * 
 * @author gizmore
 * @version 6.10
 * @since 3.0
 * @see GDT_Table
 */
abstract class MethodQueryTable extends MethodTable
{
	################
	### Abstract ###
	################
	public function getResult()
	{
	    throw new GDOException("Shuld not return result for queried methods!");
	}
    
	/**
	 * @return Query
	 */
	public function getQuery()
	{
	    return $this->gdoTable()->select('*');
	}
	
	public function getCountQuery()
	{
	    return $this->getQuery()->selectOnly('COUNT(*)');
	}
	
	protected function setupTitlePrefix()
	{
	    return 'table';
	}
	
	############
	### Exec ###
	############
	protected function setupCollection(GDT_Table $table)
	{
	    parent::setupCollection($table);
	}
	
	protected function calculateTable(GDT_Table $table)
	{
	    $table->query($this->getQuery());
	    $table->countQuery($this->getCountQuery());
	    if ($this->isPaginated())
	    {
    	    $pagemenu = $table->getPageMenu();
    	    $pagemenu->filterQuery($table->query);
	    }
	    
	}

}
