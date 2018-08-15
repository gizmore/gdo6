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
		ModuleLoader::instance()->loadModules();
		if (isset($_POST['submit']))
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
		$form->addField(GDT_Submit::make());
	}

	public function onProtect()
	{
		HTAccess::protectFolder(GWF_PATH.'temp');
		HTAccess::protectFolder(GWF_PATH.'files');
		HTAccess::protectFolder(GWF_PATH.'protected');
		HTAccess::protectFolder(GWF_PATH.'install');
		return Website::redirectMessage(GWF_WEB_ROOT)->add($this->renderPage());
	}
}
