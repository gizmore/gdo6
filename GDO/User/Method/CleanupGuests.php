<?php
namespace GDO\User\Method;

use GDO\Cronjob\MethodCronjob;
use GDO\User\GDO_User;
use GDO\Core\Application;
use GDO\Date\Time;

/**
 * Cleanup old guest accounts that are unused.
 * 
 * @author gizmore
 * @version 6.10.6
 * @since 6.10.3
 */
final class CleanupGuests extends MethodCronjob
{
	public function runEvery()
	{
		return Time::ONE_DAY;
	}
	
    public function run()
    {
        $cut = Time::getDate(Application::$TIME - GDO_SESS_TIME);
        $condition = "user_type='guest' AND user_guest_name IS NULL AND user_last_activity < '$cut'";
        $numDeleted = GDO_User::table()->deleteWhere($condition, false);
        if ($numDeleted > 0)
        {
            $this->logNotice(sprintf('Deleted %d guest users', $numDeleted));
        }
    }
    
}
