<?php
namespace GDO\User;

use GDO\DB\GDT_String;

/**
 * A Person's realname. 
 * 
 * @author gizmore
 * @version 6.10
 * @since 6.00
 */
class GDT_Realname extends GDT_String
{
	public $min = 3;
	public $max = 96;

	public $icon = 'face';

	public function defaultLabel() { return $this->label('user_real_name'); }

}
