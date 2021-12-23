<?php
namespace GDO\DB;

use GDO\Date\GDT_Timestamp;
use GDO\Date\Time;

/**
 * Automatically update 'edited at' on updates.
 * NULL on inserts.
 * 
 * @author gizmore
 * @version 6.11.0
 * @since 6.4.0
 */
final class GDT_EditedAt extends GDT_Timestamp
{
	public $writable = false;
	public $editable = false;
	public $hidden = true;
	public $orderDefaultAsc = false;
	
	public function defaultLabel() { return $this->label('edited_at'); }
	
	public function gdoBeforeUpdate(Query $query)
	{
		$now = Time::getDate();
		$query->set($this->identifier() . "=" . quote($now));
		$this->gdo->setVar($this->name, $now);
	}

	public function htmlClass()
	{
		return ' gdt-datetime';
	}
	
}
