<?php
namespace GDO\Form;

/**
 * A simple fieldless form for simple click methods.
 * 
 * @author gizmore
 */
abstract class MethodButton extends MethodForm
{
	public function createForm(GDT_Form $form)
	{
		$form->addFields([
			GDT_AntiCSRF::make(),
		]);
		$form->actions()->addField(
			GDT_Submit::make(),
		);
	}
	
}
