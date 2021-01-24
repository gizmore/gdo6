<?php
namespace GDO\DB;

use GDO\Core\Application;
use GDO\User\GDT_User;
use GDO\User\GDO_User;

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
// 	public $hidden = true;
	
	public function defaultLabel() { return $this->label('created_by'); }
	
	protected function __construct()
	{
		parent::__construct();
		$this->withCompletion();
	}
	
	/**
	 * Initial data.
	 * Force persistance on current user.
	 * {@inheritDoc}
	 * @see \GDO\Core\GDT::blankData()
	 */
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
	
	public function getValue()
	{
		$value = parent::getValue();
		return $value ? $value : GDO_User::system();
	}

}
