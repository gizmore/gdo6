<?php
namespace GDO\Form;

use GDO\UI\GDT_Bar;

/**
 * Extend a form by craeting multiple buttons.
 *
 * @author gizmore
 * @version 6.10.6
 * @since 6.10.6
 */
class MethodButtonBar extends MethodForm
{
	/**
	 * Get Submit Buttons.
	 * @return self[]
	 */
	public function getSubmitButtons()
	{
		return [
			GDT_Submit::make(), #->onclick([$this, 'formValidated']) (@FIXME and break backwards compatibility with old MethodForm.)
		];
	}

	public function createForm(GDT_Form $form)
	{
		$form->addField(GDT_AntiCSRF::make());
		$box = GDT_Bar::make()->horizontal();
		$box->addFields($this->getSubmitButtons());
		$form->actions()->addField($box);
	}

	public function formValidated(GDT_Form $form)
	{
		return $this->error('err_nothing_happened')->
			addField($this->renderPage());
	}

}
