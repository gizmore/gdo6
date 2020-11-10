<?php
namespace GDO\User;

use GDO\DB\GDT_UInt;

/**
 * User level field.
 * @author gizmore
 * @version 6.10
 * @since 6.02
 */
final class GDT_Level extends GDT_UInt
{
	public function defaultLabel() { return $this->label('level'); }
	
	public $icon = 'level';
	public $initial = '0';
	
}
