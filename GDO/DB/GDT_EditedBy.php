<?php
namespace GDO\DB;

use GDO\Core\Application;
use GDO\User\GDT_User;
use GDO\User\GDO_User;

/**
 * Automatically updates the editor user on update queries.
 * @author gizmore
 * @version 6.10.3
 * @since 6.0.0
 */
final class GDT_EditedBy extends GDT_User
{
	public $writable = false;
	public $editable = false;
	public $hidden = true;
	
	public function defaultLabel() { return $this->label('edited_by'); }
	
	public function gdoBeforeUpdate(Query $query)
	{
  		$userId = GDO_User::current()->getID();
   		$userId = $userId > 0 ? $userId : 1;
   		$query->set($this->identifier() . '=' . $userId);
   		$this->gdo->setVar($this->name, $userId);
	}

	public function blankData()
	{
	    if ($this->var)
	    {
	        return [$this->name => $this->var];
	    }
	    $user = GDO_User::current();
	    if (Application::instance()->isInstall() || (!$user->isPersisted()))
	    {
	        $user = GDO_User::system();
	    }
	    return [$this->name => $user->getID()];
	}

}
