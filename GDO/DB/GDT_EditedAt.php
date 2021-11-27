<?php
namespace GDO\DB;

use GDO\Date\GDT_Timestamp;
use GDO\Core\Application;

/**
 * Automatically update on updates.
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
		$now = Application::$MICROTIME;
		$query->set($this->identifier() . "=" . $now);
		$this->gdo->setVar($this->name, $now);
	}

}
