<?php
namespace GDO\Install\Method;

use GDO\DB\Database;
use GDO\Form\MethodForm;
use GDO\Core\GDT_Template;
use GDO\User\GDO_User;
use GDO\Core\Debug;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\User\GDO_UserPermission;
use GDO\Util\BCrypt;
use GDO\User\GDO_Permission;
use GDO\Core\ModuleLoader;
use GDO\Form\GDT_AntiCSRF;
use GDO\User\GDO_Session;

class InstallAdmins extends MethodForm
{
	public function createForm(GDT_Form $form)
	{
		Debug::init();
		Database::init();
		GDO_Session::init(GWF_SESS_NAME, GWF_SESS_DOMAIN, GWF_SESS_TIME, !GWF_SESS_JS, GWF_SESS_HTTPS);
		$hasdb = GWF_DB_HOST !== null;
		ModuleLoader::instance()->loadModules($hasdb, !$hasdb);

		$users = GDO_User::table();
		$form->addFields(array(
			$users->gdoColumn('user_name'),
			$users->gdoColumn('user_email'),
			$users->gdoColumn('user_password'),
			GDT_Submit::make(),
			GDT_AntiCSRF::make(),
		));
	}
	
	public function renderPage()
	{
		return GDT_Template::responsePHP('Install', 'page/installadmins.php', ['form' => $this->getForm()]);
	}
	
	public function formValidated(GDT_Form $form)
	{
		$password = $form->getField('user_password');
		$password->val(BCrypt::create($password->getVar())->__toString());
		
		$user = GDO_User::blank($form->getFormData())->setVars(array(
			'user_type' => GDO_User::MEMBER,
		))->insert();
		
		$permissions = ['admin', 'staff', 'cronjob'];
		foreach ($permissions as $permission)
		{
			GDO_UserPermission::grantPermission($user, GDO_Permission::getOrCreateByName($permission));
		}
		
		return parent::formValidated($form)->add($this->renderPage());
	}
	
}
