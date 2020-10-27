<?php
namespace GDO\Core;

use GDO\Table\GDT_Table;
use GDO\Util\Common;
use GDO\DB\Query;
use GDO\Table\Module_Table;

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
abstract class MethodCompletion extends MethodAjax
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
    public function gdoQuery() { return $this->gdoTable()->select()->limit($this->getMaxSuggestions()); }
    
    #############
    ### Input ###
    #############
	public function getSearchTerm() { return Common::getRequestString('query'); }
	public function getMaxSuggestions() { return Module_Table::instance()->cfgSuggestionsPerRequest(); }
	
	############
	### Exec ###
	############
	public function execute()
	{
	    $table = $this->gdoTable();
	    $query = $this->gdoQuery();
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
