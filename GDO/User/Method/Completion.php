<?php
namespace GDO\User\Method;
use GDO\Core\GDO;
use GDO\User\GDT_User;
use GDO\User\GDO_User;
use GDO\Util\Common;
use GDO\Core\Website;
use GDO\Core\MethodAjax;

/**
 * Auto completion for GDT_User types.
 * @author gizmore
 * @version 5.0
 * @since 5.0
 */
class Completion extends MethodAjax
{
	public static $MAXCOUNT = 20;
	
	public function isGuestAllowed() { return false; }
	
	public function execute()
	{
		$q = GDO::escapeS(Common::getRequestString('query'));
		$condition = sprintf('user_type IN ("guest","member") AND user_name LIKE \'%%%1$s%%\' OR user_real_name LIKE \'%%%1$s%%\' OR user_guest_name LIKE \'%%%1$s%%\'', $q);
		$query = GDO_User::table()->select('*')->where($condition)->limit(self::$MAXCOUNT)->uncached();
		$result = $query->exec();
		$response = [];
		$cell = GDT_User::make('user_id');
		
		while ($user = $result->fetchObject())
		{
			$user instanceof GDO_User;
			$response[] = array(
				'id' => $user->getID(),
				'json' => array(
					'user_name' => $user->getName(),
					'user_country' => $user->getCountryISO()
				),
				'text' => $user->displayNameLabel(),
				'display' => $cell->renderChoice($user),
			);
		}
		Website::renderJSON($response);
	}
}
