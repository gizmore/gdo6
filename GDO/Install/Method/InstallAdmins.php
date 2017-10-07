<?php
namespace GDO\Install\Method;
use GDO\DB\Database;
use GDO\Form\MethodForm;
use GDO\Core\GDT_Template;
use GDO\User\GDO_User;
use GDO\User\GDT_Password;
use GDO\Core\Debug;
use GDO\User\GDT_Username;
use GDO\Mail\GDT_Email;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\User\GDO_UserPermission;
use GDO\Util\BCrypt;
use GDO\User\GDO_Permission;

class InstallAdmins extends MethodForm
{
    public function createForm(GDT_Form $form)
    {
        Debug::init();
        Database::init();
        $form->addFields(array(
            GDT_Username::make('user_name'),
            GDT_Email::make('user_email'),
            GDT_Password::make('user_password'),
            GDT_Submit::make(),
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
            GDO_Permission::getOrCreateByName($permission);
            GDO_UserPermission::grant($user, $permission);
        }
        
        return parent::formValidated($form)->add($this->renderPage());
    }
    
}
