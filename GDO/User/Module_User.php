<?php
namespace GDO\User;
use GDO\Core\GDO_Module;
/**
 * GDO_User related types and plugins.
 * @author gizmore
 * @since 6.0
 */
final class Module_User extends GDO_Module
{
	public $module_priority = 5;
	public function isCoreModule() { return true; }
	public function onInstall() { OnInstall::onInstall(); }
	public function onLoadLanguage() { $this->loadLanguage('lang/user'); }
	public function href_administrate_module() { return href('User', 'Admin'); }
	public function getClasses()
	{
		return array(
			'GDO\User\GDO_User',
			'GDO\User\GDO_Session',
			'GDO\User\GDO_UserPermission',
			'GDO\User\GDO_UserSetting',
			'GDO\User\GDO_UserSettingBlob',
			'GDO\User\GDO_PublicKey',
		);
	}
}
