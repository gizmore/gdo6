<?php
namespace GDO\User;

use GDO\Util\BCrypt;

/**
 * Bcrypt hash form and database value
 * @author gizmore
 * @version 6.10.6
 * @since 5.0
 */
class GDT_PasswordHash extends GDT_Password
{
    public function isSerializable() { return false; }
    
    public function toValue($var)
	{
		return $var === null ? null : new BCrypt($var);
	}

}
