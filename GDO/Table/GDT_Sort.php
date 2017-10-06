<?php
namespace GDO\Table;

use GDO\DB\GDT_Int;

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
