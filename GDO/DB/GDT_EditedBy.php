<?php
namespace GDO\DB;

use GDO\User\GDT_User;
use GDO\User\GDO_User;

final class GDT_EditedBy extends GDT_User
{
	public $writable = false;
	public $editable = false;
	
	public function defaultLabel() { return $this->label('edited_by'); }
	
	public function gdoBeforeUpdate(Query $query)
	{
		$userId = GDO_User::current()->getID();
		$userId = $userId > 0 ? $userId : 1;
		$query->set($this->identifier() . '=' . $userId);
		$this->gdo->setVar($this->name, $userId);
	}
}
