<?php
namespace GDO\User\Method;

use GDO\Form\GDT_Form;
use GDO\Form\MethodForm;
use GDO\Core\GDT_Hook;
use GDO\Core\MethodAdmin;
use GDO\User\GDO_User;
use GDO\Form\GDT_Submit;
use GDO\Form\GDT_AntiCSRF;
use GDO\User\GDT_Permission;
use GDO\User\GDO_Permission;
use GDO\User\GDO_UserPermission;

/**
 * Add a user by hand.
 * @author gizmore
 * @since 6.09
 */
final class AddUser extends MethodForm
{
	use MethodAdmin;
	
	public function createForm(GDT_Form $form)
	{
		$users = GDO_User::table();
		$form->title(t('mdescr_user_adduser'));
		$form->addField($users->gdoColumn('user_name'));
		$form->addField($users->gdoColumn('user_email'));
// 		$form->addField($users->gdoColumn('user_level'));
		$form->addField(GDT_Permission::make('permissions')->multiple());
		$form->addField(GDT_Submit::make());
		$form->addField(GDT_AntiCSRF::make());
	}
	
	/**
	 * @return GDO_Permission[]
	 */
	private function getSubmittedPermissions()
	{
		return $this->getForm()->getField('permissions')->getValue();
	}

	public function formValidated(GDT_Form $form)
	{
		$user = GDO_User::blank($form->getFormData())->insert();
		
		$permissions = $this->getSubmittedPermissions();
		
		foreach ($permissions as $permission)
		{
			GDO_UserPermission::grantPermission($user, $permission);
		}

		GDT_Hook::callWithIPC('UserActivated', $user);
		
		return $this->message('msg_form_saved');
	}
	
}
