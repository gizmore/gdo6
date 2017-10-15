<?php
namespace GDO\UI;
use GDO\Core\GDT_Template;

trait WithTooltip
{
    public $tooltip;
    public function tooltip($tooltip=null) { $this->tooltip = $tooltip; return $this; }

    public function htmlTooltip()
    {
        return $this->tooltip ? GDT_Template::php('UI', 'cell/tooltip.php', ['field' => $this]) : null;
    }
    
    public static function with($tooltip)
    {
    	return self::make()->tooltip($tooltip)->render();
    }
}
