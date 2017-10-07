<?php
namespace GDO\User;
use GDO\DB\GDT_ObjectSelect;
/**
 * Permission select.
 * @author gizmore
 * @since 1.00
 * @version 6.05
 */
final class GDT_Permission extends GDT_ObjectSelect
{
	public function defaultLabel() { return $this->label('permission'); }
	
	public function __construct()
	{
	    $this->table(GDO_Permission::table());
	}
}
