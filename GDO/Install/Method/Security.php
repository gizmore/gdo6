<?php
namespace GDO\Install\Method;

use GDO\Core\ModuleLoader;
use GDO\DB\Database;
use GDO\Form\GDT_Form;
use GDO\Form\MethodForm;
use GDO\Form\GDT_Submit;
use GDO\Util\HTAccess;
use GDO\File\GDO_File;

/**
 * HTAccess-Protect certain folders.
 * Installation is then finished, redirect to web root.
 * @author gizmore
 */
final class Security extends MethodForm
{
	public function execute()
	{
		Database::init();
		ModuleLoader::instance()->loadModulesA();
		return parent::execute();
	}
	
	public function renderPage()
	{
		return $this->templatePHP('page/security.php', ['form'=>$this->getForm()]);
	}
	
	public function createForm(GDT_Form $form)
	{
		$form->actions()->addField(GDT_Submit::make()->label('protect_folders'));
	}

	public function formValidated(GDT_Form $form)
	{
	    return $this->onProtect();
	}
	
	public function onProtect()
	{
	    $this->protectFolders();
	    $this->protectDotfiles();
	    return $this->messageRedirect('msg_install_security', null, hrefDefault());
	}
	
	public function protectFolders()
	{
		HTAccess::protectFolder(GDO_PATH.'temp');
		HTAccess::protectFolder(GDO_File::filesDir());
		HTAccess::protectFolder(GDO_PATH.'protected');
		HTAccess::protectFolder(GDO_PATH.'install');
	}
	
	public function protectDotfiles()
	{
	    # TODO: Create an .htaccess rule for .git files
	}
	
}
