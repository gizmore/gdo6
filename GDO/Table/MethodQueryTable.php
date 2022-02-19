<?php
namespace GDO\Table;

use GDO\DB\Query;
use GDO\Core\GDOException;
use GDO\Core\GDT_Hook;
use GDO\Form\GDT_DeleteButton;
use GDO\User\GDO_User;
use GDO\UI\GDT_EditButton;

/**
 * A method that displays a table via a query.
 * 
 * @author gizmore
 * @version 6.11.4
 * @since 3.0.0
 * @see GDT_Table
 */
abstract class MethodQueryTable extends MethodTable
{
    public function useFetchInto() { return true; }
    
    public function gdoHeaders()
    {
    	return array_merge(
    		$this->gdoButtonHeaders(),
    		$this->gdoTable()->gdoColumnsCache());
    }
    
    protected function gdoButtonHeaders()
    {
    	$user = GDO_User::current();
    	$headers = [];
    	if ($this->isDeleteable($user))
    	{
    		$headers[] = GDT_DeleteButton::make();
    	}
    	if ($this->isUpdateable($user))
    	{
    		$headers[] = GDT_EditButton::make();
    	}
    	return $headers;
    }
    
	################
	### Abstract ###
	################
	/**
	 * This method should not be called anymore when using Queried tables.
	 * {@inheritDoc}
	 * @see \GDO\Table\MethodTable::getResult()
	 */
	public function getResult()
	{
	    throw new GDOException("Shuld not return result for queried methods!");
	}
    
	/**
	 * Override this function to return a query for your table.
	 * Defaults to select all from your GDO table.
	 * @return Query
	 */
	public function getQuery()
	{
	    return $this->gdoTable()->select();
	}
	
	/**
	 * Return a query to count items for pagination.
	 * Usually you can leave this to gdo6, letting it transform your query above.
	 * But it's possible to return an own CountQuery.
	 * @return Query
	 */
	public function getCountQuery()
	{
	    return $this->getQuery();
	}

	############
	### Exec ###
	############
	protected function beforeCalculateTable(GDT_Table $table) {}
	
	/**
	 * Calculate the GDT_Table object for queried tables.
	 * {@inheritDoc}
	 * @see \GDO\Table\MethodTable::calculateTable()
	 */
	protected function calculateTable(GDT_Table $table)
	{
	    $query = $this->getQuery();
	    GDT_Hook::callHook("MethodQueryTable_{$this->getModuleName()}_{$this->getMethodName()}", $query);
	    $table->query($query);
        if ($this->isPaginated())
	    {
            $table->countQuery($this->getCountQuery());
            $this->beforeCalculateTable($table);
            $pagemenu = $table->getPageMenu();
	        $pagemenu->filterQuery($table->query);
	    }
	    else
	    {
	        $this->beforeCalculateTable($table);
	    }
	}

}
