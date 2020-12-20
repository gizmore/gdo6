<?php
namespace GDO\User;

use GDO\Core\GDO;
use GDO\DB\GDT_CreatedAt;
use GDO\DB\GDT_CreatedBy;
use GDO\DB\GDT_Index;

/**
 * Table for user<=>permission relations.
 * @author gizmore
 * @version 6.10
 * @since 6.00
 * @see GDO_Permission
 */
final class GDO_UserPermission extends GDO
{
	public function gdoCached() { return false; }
	
	public function gdoColumns()
	{
		return array(
			GDT_User::make('perm_user_id')->primary(),
			GDT_Permission::make('perm_perm_id')->primary(),
			GDT_CreatedAt::make('perm_created_at'),
			GDT_CreatedBy::make('perm_created_by'),
		    GDT_Index::make('perm_user_id_index')->hash()->indexColumns('perm_user_id'),
		);
	}
	
	/**
	 * @return GDO_User
	 */
	public function getUser() { return $this->getValue('perm_user_id'); }
	public function getUserID() { return $this->getVar('perm_user_id'); }
	
	/**
	 * @return GDO_Permission
	 */
	public function getPermission() { return $this->getValue('perm_perm_id'); }
	public function getPermissionID() { return $this->getVar('perm_perm_id'); }

	##############
	### Static ###
	##############
	public static function load(GDO_User $user)
	{
		if (!$user->isPersisted())
		{
			return [];
		}
		return self::table()->select('perm_name, perm_level')->join("JOIN gdo_permission on perm_perm_id = perm_id")->where("perm_user_id={$user->getID()}")->order('perm_level')->exec()->fetchAllArray2dPair();
	}
	
	/**
	 * Grant via permission object.
	 * @param GDO_User $user
	 * @param GDO_Permission $permission
	 * @return static
	 */
	public static function grantPermission(GDO_User $user, GDO_Permission $permission)
	{
		return self::blank(array('perm_user_id' => $user->getID(), 'perm_perm_id' => $permission->getID()))->replace();
	}
	
	/**
	 * Grant via name.
	 * @param GDO_User $user
	 * @param string $permission
	 * @return static
	 */
	public static function grant(GDO_User $user, $permission)
	{
		return self::grantPermission($user, GDO_Permission::getByName($permission));
	}
	
	public static function revokePermission(GDO_User $user, GDO_Permission $permission)
	{
		return self::table()->deleteWhere("perm_user_id={$user->getID()} AND perm_perm_id={$permission->getID()}");
	}
	
	public static function revoke(GDO_User $user, $permission)
	{
		return self::revokePermission($user, GDO_Permission::getByName($permission));
	}
	
}
