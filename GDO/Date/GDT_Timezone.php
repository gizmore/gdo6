<?php
namespace GDO\Date;

use GDO\Form\GDT_Select;
use GDO\DB\GDT_ObjectSelect;

/**
 * Timezone select.
 * 
 * @author gizmore
 * @version 6.10
 * @since 6.10
 */
final class GDT_Timezone extends GDT_Select
{
    public $max = 64;
    
    public function defaultLabel() { return $this->label('timezone'); }
    
    protected function __construct()
    {
        $this->notNull(true);
        $this->initChoices();
        $this->initial(GWF_TIMEZONE);
        $this->icon('time');
        $this->caseS();
        $this->ascii();
        $this->completionHref(href('Core', 'TimezoneComplete'));
    }
    
    public function initChoices()
    {
        if (empty($this->choices))
        {
            $tz = array_values(timezone_identifiers_list());
            $this->choices = array_combine($tz, $tz);
        }
        return $this;
    }

}
