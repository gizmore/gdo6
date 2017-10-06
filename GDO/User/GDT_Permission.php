<?php
namespace GDO\User;
use GDO\DB\GDT_ObjectSelect;

/**
 * Permission select
 * @author gizmore
 */
final class GDT_Permission extends GDT_ObjectSelect
{
	public function defaultLabel() { return $this->label('permission'); }
	
	public function __construct()
	{
	    $this->table(GDO_Permission::table());
// 		$this->initialValue("");
	}
	
// 	private $onlyOwn = false;
// 	public function onlyOwn($onlyOwn=true)
// 	{
// 		$this->onlyOwn = $onlyOwn;
// 		return $this;
// 	}
	
}
