<?php
namespace GDO\User\Method;

use GDO\Core\GDO;
use GDO\User\GDO_User;
use GDO\Util\Common;
use GDO\Core\MethodAjax;
use GDO\Core\Module_Core;
use GDO\Core\GDT_Array;

/**
 * Auto completion for GDT_User.
 * 
 * @author gizmore
 * @version 6.10.3
 * @since 5.0.0
 */
class Completion extends MethodAjax
{
	public static $MAXCOUNT = 20;
	
	public function isGuestAllowed() { return Module_Core::instance()->cfgAllowGuests(); }
	
	public function execute()
	{
		$q = GDO::escapeS(Common::getRequestString('query'));
		$condition = sprintf('user_type IN ("guest","member") AND user_name LIKE \'%%%1$s%%\' OR user_real_name LIKE \'%%%1$s%%\' OR user_guest_name LIKE \'%%%1$s%%\'', $q);
		$query = GDO_User::table()->select()->where($condition)->limit(self::$MAXCOUNT)->uncached();
		$result = $query->exec();
		$response = [];
		
		/** @var $user GDO_User **/
		while ($user = $result->fetchObject())
		{
			$response[] = array(
				'id' => $user->getID(),
				'json' => array(
					'user_name' => $user->getName(),
					'user_country' => $user->getCountryISO()
				),
				'text' => $user->displayNameLabel(),
			    'display' => $user->renderChoice(),
			);
		}
	
		return GDT_Array::make()->data($response);
	}
}
