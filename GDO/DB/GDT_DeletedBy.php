<?php
namespace GDO\DB;
use GDO\User\GDT_User;
/**
 * @author gizmore
 */
final class GDT_DeletedBy extends GDT_User
{
	public $writable = false;
	public $editable = false;
	
	public function defaultLabel() { return $this->label('deleted_by'); }
}
