<?php
namespace GDO\Birthday;

use GDO\Date\GDT_Date;

/**
 * A birthday datatype.
 * @author gizmore
 * @version 6.10.1
 */
final class GDT_Birthdate extends GDT_Date
{
	public function defaultLabel() { return $this->label('birthdate'); }
	
	public $icon = 'birthday';
	
}
