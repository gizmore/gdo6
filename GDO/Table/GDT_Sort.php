<?php
namespace GDO\Table;

use GDO\DB\GDT_UInt;

/**
 * This GDT makes a GDO table sortable.
 * 
 * @author gizmore
 * @version 6.10
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
	}
	
	public function gdoAfterCreate()
	{
		$this->gdo->saveVar($this->name, $this->gdo->getID());
	}

}
