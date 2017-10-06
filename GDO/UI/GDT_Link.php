<?php
namespace GDO\UI;
use GDO\Core\GDT;
use GDO\Core\GDT_Template;
/**
 * An anchor for menus or paragraphs.
 * @author gizmore
 * @since 6.00
 * @version 7.00
 */
class GDT_Link extends GDT
{
    use WithIcon;
    use WithLabel;
    use WithHREF;
    
    public function renderCell() { return GDT_Template::php('UI', 'cell/link.php', ['link' => $this]); }
    public function renderForm() { return $this->renderCell(); }
 
    public static function anchor($href, $label=null)
    {
        $label = $label ? $label : $href;
        return self::make()->href($href)->rawLabel($label)->render();
    }
}
