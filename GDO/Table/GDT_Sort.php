<?php
namespace GDO\Table;

use GDO\DB\GDT_Int;

/**
 * This GDT makes a GDO table sortable.
 * @author gizmore
 * @version 6.10
 */
class GDT_Sort extends GDT_Int
{
	public function __construct()
	{
		$this->min = 0;
		$this->max = 65535;
		$this->bytes = 2;
		$this->unsigned();
		$this->label('sorting');
	}
	
	public function gdoAfterCreate()
	{
		$this->gdo->saveVar($this->name, $this->gdo->getID());
	}

}
