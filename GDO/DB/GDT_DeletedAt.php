<?php
namespace GDO\DB;

use GDO\Date\GDT_DateTime;
/**
 * @author gizmore
 * @since 5.0
 */
final class GDT_DeletedAt extends GDT_DateTime
{
	public $writable = false;
	public $editable = false;
	public $hidden = true;
	
	public function defaultLabel() { return $this->label('deleted_at'); }
}
