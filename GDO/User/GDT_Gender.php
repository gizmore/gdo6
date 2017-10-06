<?php
namespace GDO\User;
use GDO\DB\GDT_Enum;
/**
 * Gender enum.
 * @author gizmore
 */
class GDT_Gender extends GDT_Enum
{
    public function __construct()
    {
        $this->enumValues('none', 'male', 'female');
        $this->initial('none');
    }
    
    public function enumLabel($enumValue=null)
    {
        return t("gender_$enumValue");
    }
}
