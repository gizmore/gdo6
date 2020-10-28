<?php
namespace GDO\User;

use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\DB\GDT_Name;
use GDO\DB\GDT_String;
use GDO\Core\GDT_Hook;

/**
 * Similiar to modulevars, this table is for user vars.
 * When a setting is changed, a hook is called.
 * 
 * @hook UserSettingChange(GDO_User, key, var)
 * 
 * @author gizmore@wechall.net
 * @version 6.08
 * @since 6.00
 */
final class GDO_UserSetting extends GDO
{
	###############
	### Factory ###
	###############
	/**
	 * @var GDT[]
	 */
	private static $settings = [];
	public static function register(GDT $gdoType)
	{
		self::$settings[$gdoType->name] = $gdoType;
	}
	
	public static function getSetting($name)
	{
		return self::$settings[$name];
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
	
	###########
	### API ###
	###########
	public static function load(GDO_User $user)
	{
		if (!$user->isPersisted())
		{
			return array();
		}
		return
		self::table()->select('uset_name, uset_value')->
		where("uset_user={$user->getID()}")->exec()->fetchAllArray2dPair();
	}
	
	public static function get($key)
	{
		return self::userGet(GDO_User::current(), $key);
	}

	/**
	 * @param GDO_User $user
	 * @param string $key
	 * @return \GDO\Core\GDT
	 */
	public static function userGet(GDO_User $user, $key)
	{
		if (null === ($settings = $user->tempGet('gdo_setting')))
		{
			$settings = self::load($user);
			$user->tempSet('gdo_setting', $settings);
			$user->recache();
		}
		
		/** @var $gdoType GDT **/
		if ($gdoType = @self::$settings[$key])
		{
    		$var = isset($settings[$key]) ? $settings[$key] : $gdoType->initial;
    		return $gdoType->initial($var);
		}
	}
	
	public static function userGetVar(GDO_User $user, $key)
	{
	    if ($gdt = self::userGet($user, $key))
	    {
	        return $gdt->getVar();
	    }
	}
	
	public static function userGetValue(GDO_User $user, $key)
	{
	    if ($gdt = self::userGet($user, $key))
	    {
	        return $gdt->getValue();
	    }
	}
	
	public static function set($key, $var)
	{
		return self::userSet(GDO_User::current(), $key, $var);
	}
	
	public static function inc($key, $by=1)
	{
		return self::userInc(GDO_User::current(), $key, $by);
	}
	
	public static function userInc(GDO_User $user, $key, $by=1)
	{
		return self::userSet($user, $key, self::get($key)->getValue() + $by);
	}
	
	public static function userSet(GDO_User $user, $key, $var)
	{
		$userid = $user->getID();
		
		if ($var === null)
		{
			self::table()->deleteWhere("uset_user=$userid AND uset_name=".quote($key))->exec();
		}
		else
		{
			self::blank(array(
				'uset_user' => $userid,
				'uset_name' => $key,
				'uset_value' => $var
			))->replace();
		}
		$user->tempUnset('gdo_setting');
		
		self::userGet($user, $key)->var($var); # Refresh cache?
		
		GDT_Hook::callWithIPC('UserSettingChange', $user, $key, $var);
	}
	
}
