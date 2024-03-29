<?php
namespace GDO\DB;

use GDO\Date\GDT_Timestamp;
use GDO\Date\Time;

/**
 * The created at column is not null and filled upon creation.
 * It can not be edited by a user.
 * It has a default label and the default order is descending.
 * 
 * @author gizmore
 * @version 6.11.2
 * @since 5.0
 */
class GDT_CreatedAt extends GDT_Timestamp
{
	public $notNull = true;
	public $writable = false;
	public $editable = false;
	public $orderDefaultAsc = false;
	
	public function defaultLabel() { return $this->label('created_at'); }

	public function gdoColumnDefine()
	{
		return "{$this->identifier()} TIMESTAMP({$this->millis}){$this->gdoNullDefine()} DEFAULT CURRENT_TIMESTAMP({$this->millis})";
	}
	
	/**
	 * Fill with creation date timestamp.
	 * @see \GDO\Core\GDT::blankData()
	 */
	public function blankData()
	{
	    $var = $this->var ? $this->var : Time::getDate();
		return [$this->name => $var];
	}
	
	public function displayValue($var)
	{
	    return $this->gdo->gdoColumn($var)->displayLabel();
	}
	
	public function htmlClass()
	{
		return ' gdt-datetime';
	}

}
