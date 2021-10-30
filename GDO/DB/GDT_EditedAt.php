<?php
namespace GDO\DB;

use GDO\Date\Time;
use GDO\Date\GDT_DateTime;

/**
 * Automatically update on updates.
 * @author gizmore
 * @since 6.04
 */
final class GDT_EditedAt extends GDT_DateTime
{
	public $writable = false;
	public $editable = false;
	public $hidden = true;
	public $orderDefaultAsc = false;
	
	public function defaultLabel() { return $this->label('edited_at'); }
	
	public function gdoBeforeUpdate(Query $query)
	{
// 	    if (!$this->var)
// 	    {
    		$now = Time::getDate();
    		$query->set($this->identifier() . "=" . quote($now));
    		$this->gdo->setVar($this->name, $now);
// 	    }
	}

// 	public function blankData()
// 	{
// 	    return [$this->name => Time::getDate()];
// 	}

}
