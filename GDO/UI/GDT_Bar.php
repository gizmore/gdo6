<?php
namespace GDO\UI;
use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\Core\WithFields;
use GDO\Core\GDT_Hook;
/**
 * A bar is a collection of fields that can be arranged either horizontally or vertically.
 * @author gizmore
 * @version 7.00
 */
class GDT_Bar extends GDT
{
    use WithFields;

    const VERTICAL = 1;
    const HORIZONTAL = 2;

    public function renderCell() { return GDT_Template::php('UI', 'cell/bar.php', ['bar' => $this]); }
    
    public $direction = self::HORIZONTAL;
    public function direction($direction) { $this->direction = $direction; return $this; }
    public function vertical() { return $this->direction(self::VERTICAL); }
    public function horizontal() { return $this->direction(self::HORIZONTAL); }
    public function htmlDirection() { return $this->direction === self::HORIZONTAL ? 'row' : 'column'; }
    
    public function yieldHook($hookName)
    {
        GDT_Hook::call($hookName, $this);
        return $this->renderCell();
    }
}
