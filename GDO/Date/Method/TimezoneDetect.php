<?php
namespace GDO\Date\Method;

use GDO\DB\GDT_String;
use GDO\Form\GDT_Validator;
use GDO\Form\GDT_Form;
use GDO\Date\GDO_Timezone;
use GDO\Form\MethodForm;
use GDO\Form\GDT_Submit;

/**
 * Detect timezone by name.
 * 
 * @author gizmore
 */
final class TimezoneDetect extends MethodForm
{
	public function formName() { return 'tzform'; }
	
	public function createForm(GDT_Form $form)
	{
		$form->addFields([
			GDT_String::make('timezone')->notNull(),
			GDT_Validator::make('validTimezone')->validator('timezone', [$this, 'validateTimezoneName']),
		]);
		$form->actions()->addField(GDT_Submit::make());
	}
	
	public function validateTimezoneName(GDT_Form $form, GDT_String $string, $value)
	{
		if (!($this->tz = GDO_Timezone::getBy('tz_name', $value)))
		{
			return $string->error('err_unknown_timezone');
		}
		return true;
	}
	
	public function formValidated(GDT_Form $form)
	{
		$_REQUEST['tzform']['timezone'] = $this->tz->getID();
		return Timezone::make()->execute();
	}

}
