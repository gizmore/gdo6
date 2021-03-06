<?php
namespace GDO\Table;

use GDO\DB\GDT_UInt;

/**
 * This GDT makes a GDO table sortable.
 * Saves initial sorting with autoinc value.
 * 
 * @TODO on GDO with non auto-increment this will crash.
 * 
 * @author gizmore
 * @version 6.10.1
 * @since 6.3.0
 */
class GDT_Sort extends GDT_UInt
{
    public function defaultLabel() { return $this->label('sorting'); }
    
	protected function __construct()
	{
	    parent::__construct();
		$this->min = 0;
		$this->max = 65535;
		$this->bytes = 2;
		$this->notNull();
		$this->initial('0');
	}
	
	public function gdoAfterCreate()
	{
	    # @TODO use count(*) for sorting?
		$this->gdo->saveVar($this->name, $this->gdo->getID());
	}

}
