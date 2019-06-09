<?php
namespace GDO\DB;

use GDO\Core\Application;
use GDO\User\GDT_User;
use GDO\User\GDO_User;
use GDO\Core\Module_Core;

/**
 * The "CeatedBy" column is filled with current user upon creation.
 * In case the installer or maybe cli is running, the system user is used.
 * 
 * (: sdoʌǝp ˙sɹɯ ɹoɟ ƃuıʞoo⅂
 * 
 * @author gizmore
 * @since 5.0
 * @version 6.09
 */
final class GDT_CreatedBy extends GDT_User
{
	public $writable = false; # no visible in form? / no editing allowed?
	public $editable = false; # no editing allowed (disabled, nochange/ignore)
	# public $notNull = false; # should be default

	# public $cascade = "SET NULL"; # should be default
	
	public function defaultLabel() { return $this->label('created_by'); }
	
	/**
	 * Initial data.
	 * Force persistance on current user.
	 * {@inheritDoc}
	 * @see \GDO\Core\GDT::blankData()
	 */
	public function blankData()
	{
		$id = Application::instance()->isInstall()
			? Module_Core::instance()->cfgSystemUserID()
			: GDO_User::current()->persistent()->getID();
		return [$this->name => $id];
	}
}
