<?php
namespace GDO\Core;

use GDO\Table\GDT_Table;
use GDO\DB\Query;

/**
 * Generic autocompletion code.
 * Override 3 methods for full featured quicksearch.
 * 
 * @author gizmore
 * @version 6.10
 * @since 6.03
 * 
 * @see GDT_Table
 */
abstract class MethodCompletionSearch extends MethodCompletion
{
    ################
    ### Abstract ###
    ################
    /**
     * @return GDO
     */
    public abstract function gdoTable();
    
    /**
     * @return GDT[]
     */
    public abstract function gdoHeaderColumns();
    
    /**
     * @param GDO $gdo
     * @return array
     */
    public abstract function renderJSON(GDO $gdo);
    
    /**
     * @return Query
     */
    public function getQuery() { return $this->gdoTable()->select()->limit($this->getMaxSuggestions()); }
    
	############
	### Exec ###
	############
	public function execute()
	{
	    $table = $this->gdoTable();
	    $query = $this->getQuery();
	    $gdtTable = GDT_Table::make()->gdo($table);
	    $gdtTable->headersWith($this->gdoHeaderColumns());
	    $gdtTable->bigSearchQuery($query, $this->getSearchTerm());
	    $result = $query->exec();
	    $response = [];
	    while ($gdo = $result->fetchObject())
	    {
	        $response[] = $this->renderJSON($gdo);
	    }
	    Website::renderJSON($response);
	}
	
}
