<?php
namespace GDO\User;
use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\DB\GDT_Name;
use GDO\DB\GDT_String;
/**
 * Similiar to modulevars, this table is for user vars.
 * @author gizmore
 * @version 6.05
 * @since 6.00
 */
final class GDO_UserSetting extends GDO
{
	/**
	 * @var GDT[]
	 */
	private static $settings = [];
	public static function register(GDT $gdoType)
	{
		self::$settings[$gdoType->name] = $gdoType;
	}
	
	###########
	### GDO ###
	###########
	public function gdoCached() { return false; }
	public function gdoDependencies() { return ['GDO\User\GDO_User', 'GDO\Core\GDO_Module']; }
	public function gdoColumns()
	{
		return array(
		    GDT_User::make('uset_user')->primary(),
		    GDT_Name::make('uset_name')->primary(),
		    GDT_String::make('uset_value')->notNull(),
		);
	}
	
	public static function load(GDO_User $user)
	{
		return self::table()->select('uset_name, uset_value')->where("uset_user={$user->getID()}")->exec()->fetchAllArray2dPair();
	}
	
	public static function get($key)
	{
		return self::userGet(GDO_User::current(), $key);
	}

	public static function userGet(GDO_User $user, $key)
	{
		if (null === ($settings = $user->tempGet('gdo_setting')))
		{
		    $settings = self::load($user);
			$user->tempSet('gdo_setting', $settings);
			$user->recache();
		}
		$gdoType = self::$settings[$key];
		return isset($settings[$key]) ? $gdoType->initial($settings[$key]) : $gdoType;
	}

	public static function set($key, $value)
	{
		return self::userSet(GDO_User::current(), $key, $value);
	}
	
	public static function inc($key, $by=1)
	{
	    return self::userInc(GDO_User::current(), $key, $by);
	}
	
	public static function userInc(GDO_User $user, $key, $by=1)
	{
	    return self::userSet($user, $key, self::get($key)->getValue() + $by);
	}
	
	public static function userSet(GDO_User $user, $key, $value)
	{
	    $userid = $user->getID();
	    if ($value === null)
	    {
	        self::table()->deleteWhere("uset_user=$userid AND uset_name=".quote($key))->exec();
	    }
	    else
	    {
	        self::blank(array(
	            'uset_user' => $userid,
	            'uset_name' => $key,
	            'uset_value' => $value
	        ))->replace();
	    }
	    $user->tempUnset('gdo_setting');
	    $user->recache();
	}
	
}