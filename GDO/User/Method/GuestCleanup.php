<?php
namespace GDO\User\Method;
use GDO\Core\MethodCronjob;
use GDO\User\GDO_Session;
use GDO\User\GDO_User;
use GDO\Date\Time;
use GDO\DB\Database;

final class GuestCleanup extends MethodCronjob
{
	public function run()
	{
		$db = Database::instance();
		
		$cut = Time::getDate(time() - GWF_SESS_TIME);
		
		GDO_Session::table()->deleteWhere("sess_time<'$cut' OR sess_time IS NULL")->exec();
		$sessions = $db->affectedRows();
		$this->logNotice("Deleted $sessions sessions");
		
		GDO_User::table()->deleteWhere("user_type='guest' AND ( SELECT 1 FROM gdo_session WHERE user_id=sess_user ) IS NULL")->exec();
		$guests = $db->affectedRows();
		$this->logNotice("Deleted $guests guest users");
	}
	
}