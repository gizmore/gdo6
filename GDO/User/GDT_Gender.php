<?php
namespace GDO\User;

use GDO\DB\GDT_Enum;

/**
 * Gender enum.
 * @author gizmore
 * @since 4.01
 * @version 6.05
 */
class GDT_Gender extends GDT_Enum
{
	const NONE = 'none';
	const MALE = 'male';
	const FEMALE = 'female';
	
    public function __construct()
    {
    	$this->enumValues(self::NONE, self::MALE, self::FEMALE);
        $this->initial('none');
    }
    
    public function enumLabel($enumValue=null)
    {
        return t("gender_$enumValue");
    }

}
