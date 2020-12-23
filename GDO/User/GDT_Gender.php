<?php
namespace GDO\User;

use GDO\DB\GDT_Enum;

/**
 * Gender enum.
 * 
 * @author gizmore
 * @since 4.01
 * @version 6.07
 */
class GDT_Gender extends GDT_Enum
{
	const NONE = 'none';
	const MALE = 'male';
	const FEMALE = 'female';
	
	public function defaultLabel() { return $this->label('gender'); }
	
	protected function __construct()
	{
		$this->icon('gender');
		$this->enumValues(self::NONE, self::MALE, self::FEMALE);
		$this->initial(self::NONE);
		$this->notNull();
	}
	
	public function enumLabel($enumValue=null)
	{
		return t("gender_$enumValue");
	}

}
