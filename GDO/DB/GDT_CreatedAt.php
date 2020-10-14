<?php
namespace GDO\DB;
use GDO\Date\Time;
use GDO\Date\GDT_DateTime;
/**
 * The created at column is not null and filled upon creation.
 * It can not be edited by a user.
 * @author gizmore
 * @since 5.0
 */
class GDT_CreatedAt extends GDT_DateTime
{
	public $notNull = true;
	public $writable = false;
	public $editable = false;
	public $orderDefaultAsc = false;
	
	public function defaultLabel() { return $this->label('created_at'); }

	public function blankData()
	{
		return [$this->name => Time::getDate()];
	}
}
