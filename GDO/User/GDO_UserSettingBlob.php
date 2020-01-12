<?php
namespace GDO\User;
use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\DB\GDT_Name;
use GDO\DB\GDT_Text;
use GDO\Core\GDT_Hook;
/**
 * User settings for larger blob values, e.g. PMSignature.
 * 
 * @author gizmore
 * @version 6.05
 * @since 6.02
 */
final class GDO_UserSettingBlob extends GDO
{
	################
	### Settings ###
	################
	/**
	 * @var \GDO\Core\GDT
	 */
	private static $settings;
	public static function register(GDT $gdoType) { self::$settings[$gdoType->name] = $gdoType; }
	public static function isRegistered($name) { return isset(self::$settings[$name]); }
	
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
			GDT_Text::make('uset_value')->max(65535)->notNull(),
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
		if (null === ($settings = $user->tempGet('gdo_setting_blob')))
		{
			$settings = self::load($user);
			$user->tempSet('gdo_setting_blob', $settings);
			$user->recache();
		}
		$gdoType = self::$settings[$key];
		return $gdoType->initial(@$settings[$key]);
	}

	public static function set($key, $value)
	{
		return self::userSet(GDO_User::current(), $key, $value);
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
		$user->tempUnset('gdo_setting_blob');
		
		self::userGet($user, $key)->val($value);
		
		GDT_Hook::callWithIPC('UserSettingChange', $user, $key, $value);
	}
}
