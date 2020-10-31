<?php
namespace GDO\User\Method;
use GDO\Cronjob\MethodCronjob;
use GDO\Session\GDO_Session;
use GDO\Date\Time;
use GDO\DB\Database;
use GDO\Core\Application;

final class GuestCleanup extends MethodCronjob
{
	public function run()
	{
		$db = Database::instance();
		
		$cut = Time::getDate(Application::$TIME - GWF_SESS_TIME);
		
		GDO_Session::table()->deleteWhere("sess_time<'$cut' OR sess_time IS NULL")->exec();
		$sessions = $db->affectedRows();
		$this->logNotice("Deleted $sessions sessions");
		
// 		$guests = 0;
// 		$result = GDO_User::table()->select()->where("user_type='guest' AND ( SELECT 1 FROM gdo_session WHERE user_id=sess_user ) IS NULL")->exec();
// 		while ($user = $result->fetchObject())
// 		{
// 			$user->delete();
// 			GDT_Hook::callWithIPC("UserDeleted", $user);
// 			$guests++;
// 		}
// 		$this->logNotice("Deleted $guests guest users");
	}
	
}