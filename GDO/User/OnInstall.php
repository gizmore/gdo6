<?php
namespace GDO\User;
use GDO\Util\BCrypt;
use GDO\Core\Module_Core;
final class OnInstall
{
	public static function onInstall()
	{
	    $permissions = array(
	        'cronjob' => 0,
	        'staff' => 500,
	        'admin' => 1000,
	    );
	    
	    foreach ($permissions as $perm => $level)
	    {
	        GDO_Permission::create($perm, $level);
	    }
	    
		if (!($user = GDO_User::getByName('system')))
		{
			$user = GDO_User::blank(array(
				'user_id' => 1,
				'user_name' => 'system',
				'user_email' => GWF_BOT_EMAIL,
				'user_type' => 'system',
				'user_password' => BCrypt::create('system')->__toString(),
			))->replace();
		}
		Module_Core::instance()->saveConfigVar('system_user', $user->getID());
	}
}
