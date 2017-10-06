<?php
namespace GDO\UI;
trait WithIcon
{
    public $size = 14;
    public function size($size) { $this->size = $size; return $this; }

    public $icon;
    public static function iconS($icon=null)
    {
        return call_user_func(GDT_Icon::$iconProvider, $icon);
    }
    
    public static function utf8Icon($icon=null)
    {
        return $icon === null ? '' : "<md-icon class=\"material-icons icon-$icon\">$icon</md-icon>";
    }
    
    public function icon($icon=null) { return $this->rawIcon(self::iconS($icon)); }
    public function rawIcon($icon=null) { $this->icon = $icon; return $this; }
    public function htmlIcon() { return $this->icon ? $this->icon : ''; }
}
