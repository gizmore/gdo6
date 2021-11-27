<?php
namespace GDO\DB;

use GDO\Date\GDT_Timestamp;

/**
 * Mark a row as deleted.
 * 
 * @author gizmore
 * @version 6.11.0
 * @since 5.0
 */
final class GDT_DeletedAt extends GDT_Timestamp
{
	public $writable = false;
	public $editable = false;
	public $hidden = true;
	
	public function defaultLabel() { return $this->label('deleted_at'); }

}
