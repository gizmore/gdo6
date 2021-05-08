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
use GDO\Session\GDO_Session;

class InstallAdmins extends MethodForm
{
	public function createForm(GDT_Form $form)
	{
		Debug::init();
		Database::init();
		GDO_Session::init(GDO_SESS_NAME, GDO_SESS_DOMAIN, GDO_SESS_TIME, !GDO_SESS_JS, GDO_SESS_HTTPS);
		$hasdb = GDO_DB_HOST !== null;
		ModuleLoader::instance()->loadModules($hasdb, !$hasdb);

		$users = GDO_User::table();
		$form->addFields(array(
			$users->gdoColumn('user_name'),
			$users->gdoColumn('user_email'),
			$users->gdoColumn('user_password'),
		));
		$form->actions()->addField(GDT_Submit::make());
	}
	
	public function renderPage()
	{
		return GDT_Template::responsePHP('Install', 'page/installadmins.php', ['form' => $this->getForm()]);
	}
	
	public function formValidated(GDT_Form $form)
	{
		$password = $form->getField('user_password');
		$password->var(BCrypt::create($password->getVar())->__toString());
		
		$user = GDO_User::blank($form->getFormData())->setVars(array(
			'user_type' => GDO_User::MEMBER,
		))->insert();
		
		$permissions = ['admin' => 1000, 'staff' => 500, 'cronjob' => 500];
		foreach ($permissions as $permission => $level)
		{
			GDO_UserPermission::grantPermission($user, GDO_Permission::create($permission, $level));
		}
		
		return parent::formValidated($form)->addField($this->renderPage());
	}
	
}
