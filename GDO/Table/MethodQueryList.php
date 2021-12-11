<?php
namespace GDO\Table;

/**
 * Abstract class that renders a list.
 * Not filtered by default.
 *
 * @author gizmore
 * @version 6.11.0
 * @since 5.0.0
 */
abstract class MethodQueryList extends MethodQueryTable
{
    public function isFiltered() { return false; }
    
    public function gdoHeaders() { return []; }
    
    public function listName() { return 'list'; }
	
	public function gdoListMode() { return GDT_List::MODE_LIST; }
	
	public function createCollection()
	{
	    return GDT_List::make($this->listName())->
	       gdtTable($this->gdoTable())->
	       fetchAs($this->gdoFetchAs())->
	       listMode($this->gdoListMode());
	}
	
}
