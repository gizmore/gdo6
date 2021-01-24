<?php
namespace GDO\Table;

use GDO\DB\Query;
use GDO\Core\GDOException;
use GDO\Core\GDT_Hook;
use GDO\Core\GDO_Hook;

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
	    return $this->gdoTable()->select();
	}
	
	/**
	 * @return Query
	 */
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
	protected function beforeCalculateTable(GDT_Table $table) {}
	protected function calculateTable(GDT_Table $table)
	{
	    $query = $this->getQuery();
	    GDT_Hook::callHook("MethodQueryTable_{$this->getModuleName()}_{$this->getMethodName()}", $query);
	    $table->query($query);
        $table->countQuery($this->getCountQuery());
        $this->beforeCalculateTable($table);
        if ($this->isPaginated())
	    {
	        $pagemenu = $table->getPageMenu();
	        $pagemenu->filterQuery($table->query);
	    }
	}

}
