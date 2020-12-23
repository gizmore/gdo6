<?php
namespace GDO\User;

use GDO\DB\GDT_UInt;

/**
 * User level field.
 * NotNull, initial 0, because we want to do arithmetics.
 * With trophy icon.
 * 
 * @author gizmore
 * @version 6.10
 * @since 6.02
 */
final class GDT_Level extends GDT_UInt
{
	public function defaultLabel() { return $this->label('level'); }
	
	public $icon = 'level';
	public $initial = '0';
	public $notNull = true;
	
}
