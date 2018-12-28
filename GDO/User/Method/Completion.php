<?php
namespace GDO\User\Method;
use GDO\Core\Method;
use GDO\Core\GDO;
use GDO\User\GDT_User;
use GDO\User\GDO_User;
use GDO\Util\Common;
use GDO\Core\Website;

/**
 * Auto completion for GDT_User types.
 * @author gizmore
 * @version 5.0
 * @since 5.0
 */
class Completion extends Method
{
	public static $MAXCOUNT = 20;
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
				'json' => $user->renderJSON(),
				'text' => $user->displayNameLabel(),
				'display' => $cell->renderChoice($user),
			);
		}
		Website::renderJSON($response);
	}
}
