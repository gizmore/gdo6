<?php
namespace GDO\Date;

use GDO\User\GDO_User;

/**
 * Add a timezone to a gdt.
 * 
 * @author gizmore
 * @version 6.11.0
 * @since 6.10.4
 */
trait WithTimezone
{
    public $timezone = '1';

    public function userTimezone(GDO_User $user=null)
    {
        $user = $user ? $user : GDO_User::current();
        return $this->timezone($user->getTimezone());
    }

    public function timezone($timezone)
    {
        $this->timezone = $timezone;
        return $this;
    }
    
    public function getTimezoneObject()
    {
        return Time::getTimezoneObject($this->timezone);
    }
    
}
