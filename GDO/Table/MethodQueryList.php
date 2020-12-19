<?php
namespace GDO\Table;


/**
 * Abstract class that renders a list.
 *
 * @author gizmore
 * @version 6.10
 * @since 5.0
 */
abstract class MethodQueryList extends MethodQueryTable
{
	public function listName() { return 'list'; }
	
	public function gdoListMode() { return GDT_List::MODE_LIST; }
	
	public function createCollection() { return GDT_List::make($this->listName())->gdtTable($this->gdoTable())->listMode($this->gdoListMode()); }
	
	protected function setupTitlePrefix()
	{
	    return 'list';
	}
	
}

