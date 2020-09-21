<?php
namespace GDO\UI;
use GDO\DB\GDT_String;

/**
 * A field that forces you to type a certain text.
 * 
 * @author gizmore
 * @since 6.07
 */
final class GDT_Confirm extends GDT_String
{
	public $confirmation = 'iconfirm';
	public function confirmation($confirmation)
	{
		$this->confirmation = $confirmation;
		return $this->label('please_confirm_with', [t($confirmation)]);
	} 
	
	public function validate($value)
	{
		return $this->confirmation === $value ? true : $this->error('err_confirm', [t($this->confirmation)]);
	}
}
