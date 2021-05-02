<?php
namespace GDO\Install\Method;
use GDO\Core\ModuleLoader;
use GDO\DB\Database;
use GDO\Form\GDT_Form;
use GDO\Form\MethodForm;
use GDO\Form\GDT_Submit;
use GDO\Util\HTAccess;
use GDO\Core\Website;
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
		if (isset($_REQUEST['form']['submit']))
		{
			$this->onProtect();
		}
		return $this->renderPage();
	}
	
	public function renderPage()
	{
		return $this->templatePHP('page/security.php', ['form'=>$this->getForm()]);
	}
	public function createForm(GDT_Form $form)
	{
		$form->actions()->addField(GDT_Submit::make()->label('protect_folders'));
	}

	public function onProtect()
	{
	    $this->protectFolders();
	    $this->protectDotfiles();
	}
	
	public function protectFolders()
	{
		HTAccess::protectFolder(GDO_PATH.'temp');
		HTAccess::protectFolder(GDO_PATH.'files');
		HTAccess::protectFolder(GDO_PATH.'protected');
		HTAccess::protectFolder(GDO_PATH.'install');
		return Website::redirect(GWF_WEB_ROOT);
	}
	
	public function protectDotfiles()
	{
	    # TODO: Create an .htaccess rule for .git files
	}
	
}
