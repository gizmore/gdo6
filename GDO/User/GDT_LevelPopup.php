<?php
namespace GDO\User;
use GDO\UI\GDT_IconButton;
use GDO\Core\GDT_Template;
/**
 * Show a trophy with level badge.
 * A tooltip explains if your access is granted or restricted.
 * @author gizmore
 */
final class GDT_LevelPopup extends GDT_IconButton
{
    public $level = 0;
    public function level($level)
    {
        $this->level = $level;
        return $this;
    }
    
    public function renderCell()
    {
        return GDT_Template::php('User', 'cell/levelpopup.php', ['field'=>$this]);
    }
}
