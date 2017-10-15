<?php
namespace GDO\DB;

use GDO\Core\Application;
use GDO\User\GDT_User;
use GDO\User\GDO_User;
use GDO\Core\Module_Core;

/**
 * The created at column is not null and filled upon creation.
 * In case the installer is running, the system user is used.
 * 
 * @author gizmore
 * @since 5.0
 * @version 6.05
 */
final class GDT_CreatedBy extends GDT_User
{
	public $notNull = true;
	public $writable = false;
	public $editable = false;
	
	public function defaultLabel() { return $this->label('created_by'); }
	
	public function blankData()
	{
		$id = Application::instance()->isInstall()
			? Module_Core::instance()->cfgSystemUserID()
			: GDO_User::current()->persistent()->getID();
		
		return [$this->name => $id];
	}

}
