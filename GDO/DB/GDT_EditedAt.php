<?php
namespace GDO\DB;
use GDO\Date\Time;
use GDO\Date\GDT_DateTime;
/**
 * @author gizmore
 */
final class GDT_EditedAt extends GDT_DateTime
{
	public $writable = false;
	public $editable = false;
	
	public function defaultLabel() { return $this->label('edited_at'); }
	
	public function gdoBeforeCreate(Query $query)
	{
	    $now = Time::getDate();
	    $query->values(array_merge($query->values, [$this->identifier() => $now]));
	    $this->gdo->setVar($this->name, $now);
	}
	
	public function gdoBeforeUpdate(Query $query)
	{
		$now = Time::getDate();
		$query->set($this->identifier() . "=" . quote($now));
		$this->gdo->setVar($this->name, $now);
	}
}
