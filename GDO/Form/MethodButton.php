<?php
namespace GDO\Form;

abstract class MethodButton extends MethodForm
{
	public function createForm(GDT_Form $form)
	{
		$form->addFields([
			GDT_AntiCSRF::make(),
		]);
		$form->actions()->addField([
			GDT_Submit::make(),
		]);
	}
	
	public abstract function formValidated(GDT_Form $form);
	
}
