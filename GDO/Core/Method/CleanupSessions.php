<?php
namespace GDO\GWF\Method;

use GDO\Cronjob\MethodCronjob;
use GDO\DB\Database;
use GDO\User\GDO_Session;
/**
 * Cronjob that deletes old sessions.
 * 
 * @author gizmore
 * @version 5.0
 * @since 1.0
 * 
 * @see Login_Form
 * @see Login_Logout
 * @see Register_Activate
 * @see Register_Guest
 */
final class CleanupSessions extends MethodCronjob
{
	public function run()
	{
		$cut = time() - GWF_SESS_TIME;
		GDO_Session::table()->deleteWhere("sess_time < {$cut}")->exec();
		if (0 < ($deleted = Database::instance()->affectedRows()))
		{
			$this->log("Deleted $deleted sessions.");
		}
	}
}
