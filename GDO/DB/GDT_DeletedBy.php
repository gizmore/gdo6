<?php
namespace GDO\DB;

use GDO\User\GDT_User;

/**
 * GDT signals deletion for a row. Some stuff auto-detects that.
 * Not often used yet.
 * 
 * @author gizmore
 * @version 6.11.2
 */
final class GDT_DeletedBy extends GDT_User
{
	public $writable = false;
	public $editable = false;
	public $hidden = true;
	public $autojoin = false;
	
	public function defaultLabel() { return $this->label('deleted_by'); }

}
