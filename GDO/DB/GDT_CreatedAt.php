<?php
namespace GDO\DB;

use GDO\Date\Time;
use GDO\Date\GDT_DateTime;

/**
 * The created at column is not null and filled upon creation.
 * It can not be edited by a user.
 * It has a default label and the default order is descending.
 * 
 * @author gizmore
 * @version 6.10
 * @since 5.0
 */
class GDT_CreatedAt extends GDT_DateTime
{
	public $notNull = true;
	public $writable = false;
	public $editable = false;
// 	public $hidden = true;
	public $orderDefaultAsc = false;
	
	public function defaultLabel() { return $this->label('created_at'); }

	/**
	 * Fill with creation date timestamp.
	 * @see \GDO\Core\GDT::blankData()
	 */
	public function blankData()
	{
		return [$this->name => Time::getDate()];
	}
	
	public function displayValue($var)
	{
	    return $this->gdo->gdoColumn($var)->displayLabel();
	}

}
