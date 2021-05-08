<?php
namespace GDO\Install\Method;

use GDO\Core\GDT_Template;
use GDO\File\GDT_File;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;

/**
 * Import a backup.
 * Requires Module_Backup.
 * @author gizmore
 */
final class ImportBackup extends MethodForm
{
	public function renderPage()
	{
		return GDT_Template::responsePHP('Install', 'page/importbackup.php', ['form' => $this->getForm()]);
	}
	
	public function createForm(GDT_Form $form)
	{
		$form->addFields(array(
			GDT_File::make('backup_file'),
		));
		$form->actions()->addField(GDT_Submit::make());
	}
	
	public function formValidated(GDT_Form $form)
	{
		if (module_enabled('Backup'))
		{
			return \GDO\Backup\Method\ImportBackup::make()->importBackup($form->getFormValue('backup_file'));
		}
		
		return parent::formValidated($form)->addField($this->renderPage());
	}
	
}
